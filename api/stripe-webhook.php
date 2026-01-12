<?php
/**
 * API: Webhook Stripe
 *
 * Recebe notificações do Stripe sobre eventos de pagamento
 * Quando pagamento é confirmado, dispara o n8n para gerar o livro
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../services/OrderService.php';
require_once __DIR__ . '/../services/PaymentService.php';

// Log de webhook recebido
error_log("Stripe webhook recebido: " . file_get_contents('php://input'));

try {
    // Obter payload e signature
    $payload = file_get_contents('php://input');
    $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

    // Validar e processar webhook
    $event = PaymentService::handleWebhook($payload, $sigHeader);

    if (!$event) {
        http_response_code(400);
        echo json_encode(['error' => 'Webhook inválido']);
        exit;
    }

    // Processar evento
    switch ($event['type']) {
        case 'checkout.session.completed':
            // Pagamento confirmado!
            $session = $event['data']['object'];
            $orderId = $session['metadata']['order_id'] ?? null;

            if ($orderId) {
                // Atualizar status do pedido para 'processing'
                OrderService::updateOrderStatus($orderId, 'processing');

                // DISPARAR N8N - Apenas após pagamento confirmado
                require_once __DIR__ . '/../services/N8nService.php';

                $order = OrderService::getOrder($orderId);

                if ($order) {
                    N8nService::triggerBookGeneration(
                        $orderId,
                        $order['child_name'],
                        $order['child_photo_url'],
                        $order['theme'],
                        [
                            'child_age' => $order['child_age'],
                            'product_type' => $order['product_type'],
                            'user_email' => $order['metadata']['user_email'] ?? ''
                        ]
                    );

                    error_log("n8n disparado para pedido #{$orderId} após pagamento confirmado");
                }
            }
            break;

        case 'payment_intent.succeeded':
            // Payment Intent bem-sucedido
            error_log("Payment Intent succeeded: " . $event['data']['object']['id']);
            break;

        case 'payment_intent.payment_failed':
            // Pagamento falhou
            $paymentIntent = $event['data']['object'];
            $orderId = $paymentIntent['metadata']['order_id'] ?? null;

            if ($orderId) {
                OrderService::updateOrderStatus($orderId, 'failed');
                error_log("Pagamento falhou para pedido #{$orderId}");
            }
            break;

        default:
            // Evento não tratado
            error_log("Evento Stripe não tratado: " . $event['type']);
    }

    // Retornar 200 para o Stripe
    echo json_encode(['received' => true]);

} catch (Exception $e) {
    error_log("Erro no webhook Stripe: " . $e->getMessage());

    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
