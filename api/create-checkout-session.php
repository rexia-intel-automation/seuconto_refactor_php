<?php
/**
 * API: Criar Sessão de Checkout Stripe
 *
 * Cria uma sessão de checkout do Stripe para o pedido
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../services/OrderService.php';
require_once __DIR__ . '/../services/PaymentService.php';

// Verificar autenticação
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Usuário não autenticado'
    ]);
    exit;
}

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Método não permitido. Use POST.'
    ]);
    exit;
}

// Obter dados JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || empty($data['order_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'order_id é obrigatório'
    ]);
    exit;
}

$orderId = intval($data['order_id']);
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

    // Verificar se o pedido pertence ao usuário
    if ($order['user_id'] !== $user['id']) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'Você não tem permissão para acessar este pedido'
        ]);
        exit;
    }

    // Verificar se o pedido já foi pago
    if ($order['status'] !== 'pending') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Este pedido já foi processado'
        ]);
        exit;
    }

    // Criar sessão de checkout no Stripe
    $checkoutSession = PaymentService::createCheckoutSession(
        $order['amount'],
        [
            'order_id' => $orderId,
            'user_id' => $user['id'],
            'product_type' => $order['product_type'],
            'child_name' => $order['child_name']
        ],
        url('api/payment-success.php?order_id=' . $orderId), // success_url
        url('pages/create/step4-checkout.php?order_id=' . $orderId . '&canceled=1') // cancel_url
    );

    // Retornar sessão
    echo json_encode([
        'success' => true,
        'data' => [
            'session_id' => $checkoutSession['id'],
            'session_url' => $checkoutSession['url']
        ]
    ]);

} catch (Exception $e) {
    error_log("Erro ao criar checkout session: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao criar sessão de pagamento. Tente novamente.'
    ]);
}
