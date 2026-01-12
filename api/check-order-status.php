<?php
/**
 * API: Verificar Status do Pedido
 *
 * Permite ao frontend fazer polling para verificar o status do pedido
 * Usado na tela de processamento (step 3)
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../services/OrderService.php';

// Verificar autenticação
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Usuário não autenticado'
    ]);
    exit;
}

// Verificar parâmetro order_id
$orderId = intval($_GET['order_id'] ?? 0);

if (!$orderId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'order_id é obrigatório'
    ]);
    exit;
}

$user = getCurrentUser();

try {
    // Buscar pedido
    $order = OrderService::getOrder($orderId);

    if (!$order) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Pedido não encontrado'
        ]);
        exit;
    }

    // Verificar permissão (usuário dono ou admin)
    if ($order['user_id'] !== $user['id'] && !isAdmin()) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'Você não tem permissão para acessar este pedido'
        ]);
        exit;
    }

    // Calcular progresso baseado no status
    $progressMap = [
        'pending' => 10,
        'processing' => 50,
        'completed' => 100,
        'failed' => 0,
        'cancelled' => 0
    ];

    $progress = $progressMap[$order['status']] ?? 0;

    // Mensagens de status
    $statusMessages = [
        'pending' => 'Aguardando pagamento...',
        'processing' => 'Criando sua história mágica com IA... ✨',
        'completed' => 'Livro pronto! Preparando download...',
        'failed' => 'Ops! Algo deu errado na geração.',
        'cancelled' => 'Pedido cancelado.'
    ];

    $statusMessage = $statusMessages[$order['status']] ?? 'Processando...';

    // Retornar status
    echo json_encode([
        'success' => true,
        'data' => [
            'order_id' => $order['id'],
            'status' => $order['status'],
            'status_message' => $statusMessage,
            'progress' => $progress,
            'book_file_url' => $order['book_file_url'] ?? null,
            'created_at' => $order['created_at'],
            'updated_at' => $order['updated_at'],
            'child_name' => $order['child_name'],
            'theme' => $order['theme']
        ]
    ]);

} catch (Exception $e) {
    error_log("Erro ao verificar status do pedido: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao verificar status do pedido'
    ]);
}
