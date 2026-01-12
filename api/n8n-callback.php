<?php
/**
 * API: Callback n8n
 *
 * Recebe notificação do n8n quando o livro estiver pronto
 * Atualiza o status do pedido e salva a URL do PDF
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../services/OrderService.php';
require_once __DIR__ . '/../services/N8nService.php';

// Log de callback recebido
error_log("n8n callback recebido: " . file_get_contents('php://input'));

// Obter dados JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Dados inválidos'
    ]);
    exit;
}

try {
    // Validar callback usando N8nService
    if (!N8nService::validateCallback($data)) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Callback inválido ou não autorizado'
        ]);
        exit;
    }

    // Processar callback
    $result = N8nService::processCallback($data);

    if ($result['success']) {
        $orderId = $result['order_id'];
        $status = $result['status'];
        $bookFileUrl = $result['book_file_url'] ?? null;

        // Atualizar pedido
        OrderService::updateOrderStatus($orderId, $status, $bookFileUrl);

        // Enviar email se completado
        if ($status === 'completed' && $bookFileUrl) {
            require_once __DIR__ . '/../includes/mailer.php';

            $order = OrderService::getOrder($orderId);
            if ($order) {
                $userEmail = $order['metadata']['user_email'] ?? null;
                $userName = $order['metadata']['user_name'] ?? 'Cliente';

                if ($userEmail) {
                    sendBookReadyEmail($userEmail, $userName, $order);
                }
            }
        }

        error_log("Pedido #{$orderId} atualizado para status: {$status}");

        echo json_encode([
            'success' => true,
            'message' => 'Callback processado com sucesso'
        ]);
    } else {
        throw new Exception($result['error'] ?? 'Erro ao processar callback');
    }

} catch (Exception $e) {
    error_log("Erro ao processar callback n8n: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao processar callback'
    ]);
}
