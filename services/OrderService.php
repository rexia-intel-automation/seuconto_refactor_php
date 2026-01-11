<?php
/**
 * OrderService - Gerenciamento de Pedidos
 *
 * Centraliza toda lógica de negócio relacionada a pedidos de livros
 */

class OrderService {
    /**
     * Conexão com banco de dados
     * @var PDO
     */
    private static $db;

    /**
     * Inicializa conexão com banco
     */
    private static function init() {
        if (self::$db === null) {
            if (!function_exists('getDB')) {
                require_once __DIR__ . '/../config/db.php';
            }
            self::$db = getDB();
        }
    }

    /**
     * Cria novo pedido
     *
     * @param array $data Dados do pedido
     * @return array Pedido criado
     * @throws Exception Se falhar ao criar
     */
    public static function createOrder($data) {
        self::init();

        // Valida dados obrigatórios
        $required = ['user_id', 'child_name', 'theme', 'price'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Campo obrigatório ausente: {$field}");
            }
        }

        $sql = "INSERT INTO orders (
            user_id, customer_email, customer_name, customer_phone,
            child_name, child_age, child_gender,
            theme, characteristics, dedication,
            photo_path, price, status,
            created_at, updated_at
        ) VALUES (
            :user_id, :customer_email, :customer_name, :customer_phone,
            :child_name, :child_age, :child_gender,
            :theme, :characteristics, :dedication,
            :photo_path, :price, :status,
            NOW(), NOW()
        )";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':customer_email' => $data['customer_email'] ?? null,
            ':customer_name' => $data['customer_name'] ?? null,
            ':customer_phone' => $data['customer_phone'] ?? null,
            ':child_name' => $data['child_name'],
            ':child_age' => $data['child_age'] ?? null,
            ':child_gender' => $data['child_gender'] ?? null,
            ':theme' => $data['theme'],
            ':characteristics' => $data['characteristics'] ?? null,
            ':dedication' => $data['dedication'] ?? null,
            ':photo_path' => $data['photo_path'] ?? null,
            ':price' => $data['price'],
            ':status' => $data['status'] ?? 'pending'
        ]);

        $orderId = self::$db->lastInsertId();

        return self::getOrder($orderId);
    }

    /**
     * Obtém pedido por ID
     *
     * @param int $orderId ID do pedido
     * @return array|null Dados do pedido ou null
     */
    public static function getOrder($orderId) {
        self::init();

        $sql = "SELECT * FROM orders WHERE id = :id LIMIT 1";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([':id' => $orderId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Obtém pedidos de um usuário
     *
     * @param int $userId ID do usuário
     * @param array $filters Filtros opcionais (status, limit, offset)
     * @return array Lista de pedidos
     */
    public static function getUserOrders($userId, $filters = []) {
        self::init();

        $sql = "SELECT * FROM orders WHERE user_id = :user_id";

        // Filtro por status
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
        }

        $sql .= " ORDER BY created_at DESC";

        // Paginação
        if (isset($filters['limit'])) {
            $sql .= " LIMIT :limit";
            if (isset($filters['offset'])) {
                $sql .= " OFFSET :offset";
            }
        }

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        if (!empty($filters['status'])) {
            $stmt->bindValue(':status', $filters['status'], PDO::PARAM_STR);
        }

        if (isset($filters['limit'])) {
            $stmt->bindValue(':limit', (int)$filters['limit'], PDO::PARAM_INT);
            if (isset($filters['offset'])) {
                $stmt->bindValue(':offset', (int)$filters['offset'], PDO::PARAM_INT);
            }
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Atualiza status do pedido
     *
     * @param int $orderId ID do pedido
     * @param string $status Novo status
     * @param string|null $bookFileUrl URL do arquivo (se completado)
     * @param string|null $errorMessage Mensagem de erro (se falhou)
     * @return bool True se atualizado
     */
    public static function updateOrderStatus($orderId, $status, $bookFileUrl = null, $errorMessage = null) {
        self::init();

        $sql = "UPDATE orders SET
                status = :status,
                book_file_url = :book_file_url,
                error_message = :error_message,
                updated_at = NOW()
                WHERE id = :id";

        $stmt = self::$db->prepare($sql);
        $result = $stmt->execute([
            ':id' => $orderId,
            ':status' => $status,
            ':book_file_url' => $bookFileUrl,
            ':error_message' => $errorMessage
        ]);

        // Se livro foi concluído, envia email de notificação
        if ($result && $status === 'completed' && $bookFileUrl) {
            $order = self::getOrder($orderId);
            if ($order && function_exists('sendBookReadyEmail')) {
                sendBookReadyEmail(
                    $order['customer_email'],
                    $order['customer_name'],
                    $order
                );
            }
        }

        return $result;
    }

    /**
     * Atualiza informações de pagamento
     *
     * @param int $orderId ID do pedido
     * @param array $paymentData Dados do pagamento
     * @return bool True se atualizado
     */
    public static function updatePayment($orderId, $paymentData) {
        self::init();

        $sql = "UPDATE orders SET
                stripe_payment_intent_id = :payment_intent_id,
                stripe_charge_id = :charge_id,
                payment_status = :payment_status,
                paid_at = :paid_at,
                updated_at = NOW()
                WHERE id = :id";

        $stmt = self::$db->prepare($sql);

        return $stmt->execute([
            ':id' => $orderId,
            ':payment_intent_id' => $paymentData['payment_intent_id'] ?? null,
            ':charge_id' => $paymentData['charge_id'] ?? null,
            ':payment_status' => $paymentData['payment_status'] ?? null,
            ':paid_at' => $paymentData['paid_at'] ?? null
        ]);
    }

    /**
     * Cancela pedido
     *
     * @param int $orderId ID do pedido
     * @param string $reason Motivo do cancelamento
     * @return bool True se cancelado
     */
    public static function cancelOrder($orderId, $reason = '') {
        self::init();

        // Se tiver geração em andamento, cancela no n8n
        $order = self::getOrder($orderId);
        if ($order && in_array($order['status'], ['generating', 'processing'])) {
            if (class_exists('N8nService')) {
                try {
                    N8nService::cancelGeneration($orderId);
                } catch (Exception $e) {
                    // Continua mesmo se n8n falhar
                }
            }
        }

        $sql = "UPDATE orders SET
                status = 'cancelled',
                error_message = :reason,
                updated_at = NOW()
                WHERE id = :id";

        $stmt = self::$db->prepare($sql);

        return $stmt->execute([
            ':id' => $orderId,
            ':reason' => $reason
        ]);
    }

    /**
     * Obtém estatísticas gerais de pedidos
     *
     * @return array Estatísticas
     */
    public static function getOverallStats() {
        self::init();

        $sql = "SELECT
                COUNT(*) as total_orders,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_orders,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
                COUNT(CASE WHEN status IN ('generating', 'processing') THEN 1 END) as in_progress_orders,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed_orders,
                SUM(price) as total_revenue,
                AVG(price) as average_order_value
                FROM orders";

        $stmt = self::$db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtém pedidos recentes (últimas 24h)
     *
     * @param int $limit Limite de resultados
     * @return array Lista de pedidos
     */
    public static function getRecentOrders($limit = 10) {
        self::init();

        $sql = "SELECT * FROM orders
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                ORDER BY created_at DESC
                LIMIT :limit";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca pedidos por critério
     *
     * @param string $query Termo de busca
     * @param array $filters Filtros adicionais
     * @return array Lista de pedidos
     */
    public static function searchOrders($query, $filters = []) {
        self::init();

        $sql = "SELECT * FROM orders WHERE (
                child_name LIKE :query OR
                customer_name LIKE :query OR
                customer_email LIKE :query OR
                id LIKE :query
            )";

        // Filtros adicionais
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND created_at >= :date_from";
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND created_at <= :date_to";
        }

        $sql .= " ORDER BY created_at DESC LIMIT 50";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':query', "%{$query}%", PDO::PARAM_STR);

        if (!empty($filters['status'])) {
            $stmt->bindValue(':status', $filters['status'], PDO::PARAM_STR);
        }

        if (!empty($filters['date_from'])) {
            $stmt->bindValue(':date_from', $filters['date_from'], PDO::PARAM_STR);
        }

        if (!empty($filters['date_to'])) {
            $stmt->bindValue(':date_to', $filters['date_to'], PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtém distribuição de pedidos por tema
     *
     * @return array Temas e contagens
     */
    public static function getOrdersByTheme() {
        self::init();

        $sql = "SELECT theme, COUNT(*) as count
                FROM orders
                GROUP BY theme
                ORDER BY count DESC";

        $stmt = self::$db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtém tempo médio de processamento
     *
     * @return float Tempo em minutos
     */
    public static function getAverageProcessingTime() {
        self::init();

        $sql = "SELECT AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_time
                FROM orders
                WHERE status = 'completed'
                AND updated_at IS NOT NULL";

        $stmt = self::$db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (float)($result['avg_time'] ?? 0);
    }
}
