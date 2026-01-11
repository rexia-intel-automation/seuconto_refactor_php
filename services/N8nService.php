<?php
/**
 * N8nService - Integração com n8n para geração de livros
 *
 * Gerencia comunicação com workflows n8n para geração de conteúdo AI
 */

class N8nService {
    /**
     * URL do webhook n8n
     * @var string
     */
    private static $webhookUrl;

    /**
     * Secret para autenticação
     * @var string
     */
    private static $webhookSecret;

    /**
     * Inicializa configurações
     */
    private static function init() {
        if (self::$webhookUrl === null) {
            if (!function_exists('env')) {
                require_once __DIR__ . '/../config/env.php';
            }
            self::$webhookUrl = env('N8N_WEBHOOK_URL');
            self::$webhookSecret = env('N8N_WEBHOOK_SECRET');
        }
    }

    /**
     * Dispara workflow de geração de livro
     *
     * @param string $orderId ID do pedido
     * @param string $childName Nome da criança
     * @param string $photoPath Caminho da foto
     * @param string $theme Tema do livro
     * @param array $metadata Dados adicionais (idade, gênero, características, etc)
     * @return array Resposta do webhook
     * @throws Exception Se webhook falhar
     */
    public static function triggerBookGeneration($orderId, $childName, $photoPath, $theme, $metadata = []) {
        self::init();

        if (empty(self::$webhookUrl)) {
            throw new Exception('N8N_WEBHOOK_URL não configurado');
        }

        // Valida se arquivo existe
        if (!file_exists($photoPath)) {
            throw new Exception("Arquivo de foto não encontrado: {$photoPath}");
        }

        // Prepara payload multipart
        $data = [
            'order_id' => $orderId,
            'child_name' => $childName,
            'theme' => $theme,
            'photo' => new CURLFile($photoPath, mime_content_type($photoPath), basename($photoPath))
        ];

        // Adiciona metadata
        if (!empty($metadata)) {
            $data['age'] = $metadata['age'] ?? null;
            $data['gender'] = $metadata['gender'] ?? null;
            $data['characteristics'] = $metadata['characteristics'] ?? '';
            $data['dedication'] = $metadata['dedication'] ?? '';
        }

        // Adiciona secret se configurado
        if (!empty(self::$webhookSecret)) {
            $data['webhook_secret'] = self::$webhookSecret;
        }

        // URL de callback
        if (defined('BASE_URL')) {
            $data['callback_url'] = BASE_URL . '/api/n8n-callback.php';
        }

        return self::sendRequest(self::$webhookUrl, $data);
    }

    /**
     * Verifica status de geração via n8n
     *
     * @param string $orderId ID do pedido
     * @return array Status da geração
     */
    public static function checkGenerationStatus($orderId) {
        self::init();

        $statusUrl = self::$webhookUrl . '/status';

        $data = [
            'order_id' => $orderId
        ];

        if (!empty(self::$webhookSecret)) {
            $data['webhook_secret'] = self::$webhookSecret;
        }

        return self::sendRequest($statusUrl, $data, 'GET');
    }

    /**
     * Cancela geração em andamento
     *
     * @param string $orderId ID do pedido
     * @return array Resposta do webhook
     */
    public static function cancelGeneration($orderId) {
        self::init();

        $cancelUrl = self::$webhookUrl . '/cancel';

        $data = [
            'order_id' => $orderId
        ];

        if (!empty(self::$webhookSecret)) {
            $data['webhook_secret'] = self::$webhookSecret;
        }

        return self::sendRequest($cancelUrl, $data, 'POST');
    }

    /**
     * Envia requisição para n8n
     *
     * @param string $url URL do endpoint
     * @param array $data Dados a enviar
     * @param string $method Método HTTP (GET, POST)
     * @return array Resposta formatada
     * @throws Exception Se requisição falhar
     */
    private static function sendRequest($url, $data, $method = 'POST') {
        $ch = curl_init();

        if ($method === 'GET') {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } else {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Log da requisição
        self::logRequest($url, $data, $httpCode, $response, $error);

        if ($error) {
            throw new Exception("Erro na requisição para n8n: {$error}");
        }

        if ($httpCode >= 400) {
            throw new Exception("n8n retornou erro HTTP {$httpCode}: {$response}");
        }

        $responseData = json_decode($response, true);

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'data' => $responseData ?? [],
            'raw_response' => $response
        ];
    }

    /**
     * Registra log de requisição para n8n
     *
     * @param string $url URL da requisição
     * @param array $data Dados enviados
     * @param int $httpCode Código HTTP de resposta
     * @param string $response Resposta recebida
     * @param string $error Erro se houver
     */
    private static function logRequest($url, $data, $httpCode, $response, $error = '') {
        $logDir = __DIR__ . '/../logs';

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/n8n-requests.log';

        // Remove dados sensíveis do log
        $sanitizedData = $data;
        if (isset($sanitizedData['webhook_secret'])) {
            $sanitizedData['webhook_secret'] = '***REDACTED***';
        }
        if (isset($sanitizedData['photo']) && $sanitizedData['photo'] instanceof CURLFile) {
            $sanitizedData['photo'] = '[FILE: ' . $sanitizedData['photo']->getFilename() . ']';
        }

        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'url' => $url,
            'data' => $sanitizedData,
            'http_code' => $httpCode,
            'response' => mb_substr($response, 0, 500), // Primeiros 500 caracteres
            'error' => $error
        ];

        $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . PHP_EOL;

        @file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    }

    /**
     * Valida callback recebido do n8n
     *
     * @param array $payload Dados do callback
     * @return bool True se válido
     */
    public static function validateCallback($payload) {
        self::init();

        // Valida secret se configurado
        if (!empty(self::$webhookSecret)) {
            $receivedSecret = $payload['webhook_secret'] ?? '';

            if (!hash_equals(self::$webhookSecret, $receivedSecret)) {
                return false;
            }
        }

        // Valida campos obrigatórios
        $requiredFields = ['order_id', 'status'];

        foreach ($requiredFields as $field) {
            if (!isset($payload[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Processa callback do n8n
     *
     * @param array $payload Dados do callback
     * @return array Resultado do processamento
     */
    public static function processCallback($payload) {
        if (!self::validateCallback($payload)) {
            return [
                'success' => false,
                'message' => 'Callback inválido'
            ];
        }

        $orderId = $payload['order_id'];
        $status = $payload['status'];
        $bookFileUrl = $payload['book_file_url'] ?? null;
        $errorMessage = $payload['error_message'] ?? null;

        // Carrega OrderService se disponível
        if (class_exists('OrderService')) {
            OrderService::updateOrderStatus($orderId, $status, $bookFileUrl, $errorMessage);
        }

        // Log do callback
        self::logCallback($payload);

        return [
            'success' => true,
            'message' => 'Callback processado com sucesso'
        ];
    }

    /**
     * Registra log de callback recebido
     *
     * @param array $payload Dados do callback
     */
    private static function logCallback($payload) {
        $logDir = __DIR__ . '/../logs';

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/n8n-callbacks.log';

        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'payload' => $payload
        ];

        $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . PHP_EOL;

        @file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    }

    /**
     * Método legado para compatibilidade
     * @deprecated Use triggerBookGeneration() em vez disso
     */
    public static function triggerWorkflow($clientName, $photoPath, $theme) {
        return self::triggerBookGeneration(
            uniqid('order_'),
            $clientName,
            $photoPath,
            $theme
        );
    }
}
