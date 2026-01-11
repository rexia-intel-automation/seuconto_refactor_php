<?php
/**
 * PaymentService - Integração com Stripe
 *
 * Gerencia todo fluxo de pagamentos via Stripe
 */

class PaymentService {
    /**
     * Chave secreta do Stripe
     * @var string
     */
    private static $secretKey;

    /**
     * Chave pública do Stripe
     * @var string
     */
    private static $publishableKey;

    /**
     * Inicializa configurações
     */
    private static function init() {
        if (self::$secretKey === null) {
            if (!function_exists('env')) {
                require_once __DIR__ . '/../config/env.php';
            }
            self::$secretKey = env('STRIPE_SECRET_KEY');
            self::$publishableKey = env('STRIPE_PUBLISHABLE_KEY');

            // Configura Stripe SDK se disponível
            if (class_exists('\Stripe\Stripe')) {
                \Stripe\Stripe::setApiKey(self::$secretKey);
            }
        }
    }

    /**
     * Cria Payment Intent para checkout
     *
     * @param int $amount Valor em centavos
     * @param array $metadata Metadados do pedido
     * @param string $currency Moeda (padrão: BRL)
     * @return array Payment Intent criado
     * @throws Exception Se Stripe falhar
     */
    public static function createPaymentIntent($amount, $metadata = [], $currency = 'brl') {
        self::init();

        if (!class_exists('\Stripe\PaymentIntent')) {
            throw new Exception('Stripe SDK não está instalado. Execute: composer require stripe/stripe-php');
        }

        try {
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amount,
                'currency' => $currency,
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true
                ]
            ]);

            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception("Erro ao criar Payment Intent: " . $e->getMessage());
        }
    }

    /**
     * Confirma pagamento
     *
     * @param string $paymentIntentId ID do Payment Intent
     * @return array Resultado da confirmação
     * @throws Exception Se falhar
     */
    public static function confirmPayment($paymentIntentId) {
        self::init();

        if (!class_exists('\Stripe\PaymentIntent')) {
            throw new Exception('Stripe SDK não está instalado');
        }

        try {
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

            return [
                'success' => $paymentIntent->status === 'succeeded',
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount,
                'charge_id' => $paymentIntent->charges->data[0]->id ?? null,
                'payment_method' => $paymentIntent->payment_method ?? null
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception("Erro ao confirmar pagamento: " . $e->getMessage());
        }
    }

    /**
     * Cria sessão de checkout (Stripe Checkout)
     *
     * @param array $lineItems Itens do checkout
     * @param array $options Opções adicionais
     * @return array Sessão criada
     * @throws Exception Se falhar
     */
    public static function createCheckoutSession($lineItems, $options = []) {
        self::init();

        if (!class_exists('\Stripe\Checkout\Session')) {
            throw new Exception('Stripe SDK não está instalado');
        }

        $baseUrl = defined('BASE_URL') ? BASE_URL : '';

        $sessionData = [
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $options['success_url'] ?? $baseUrl . '/pages/checkout-success.php?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $options['cancel_url'] ?? $baseUrl . '/pages/checkout-cancel.php',
        ];

        // Adiciona metadata se fornecido
        if (!empty($options['metadata'])) {
            $sessionData['metadata'] = $options['metadata'];
        }

        // Adiciona email do cliente se fornecido
        if (!empty($options['customer_email'])) {
            $sessionData['customer_email'] = $options['customer_email'];
        }

        try {
            $session = \Stripe\Checkout\Session::create($sessionData);

            return [
                'success' => true,
                'session_id' => $session->id,
                'session_url' => $session->url
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception("Erro ao criar sessão de checkout: " . $e->getMessage());
        }
    }

    /**
     * Processa webhook do Stripe
     *
     * @param string $payload Corpo da requisição
     * @param string $signature Header de assinatura
     * @return array Evento processado
     * @throws Exception Se assinatura for inválida
     */
    public static function handleWebhook($payload, $signature) {
        self::init();

        $webhookSecret = env('STRIPE_WEBHOOK_SECRET');

        if (empty($webhookSecret)) {
            throw new Exception('STRIPE_WEBHOOK_SECRET não configurado');
        }

        if (!class_exists('\Stripe\Webhook')) {
            throw new Exception('Stripe SDK não está instalado');
        }

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            throw new Exception('Payload inválido');
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            throw new Exception('Assinatura inválida');
        }

        // Processa evento
        return self::processWebhookEvent($event);
    }

    /**
     * Processa evento do webhook
     *
     * @param object $event Evento do Stripe
     * @return array Resultado do processamento
     */
    private static function processWebhookEvent($event) {
        $type = $event->type;
        $object = $event->data->object;

        switch ($type) {
            case 'payment_intent.succeeded':
                return self::handlePaymentSuccess($object);

            case 'payment_intent.payment_failed':
                return self::handlePaymentFailure($object);

            case 'charge.refunded':
                return self::handleRefund($object);

            case 'checkout.session.completed':
                return self::handleCheckoutCompleted($object);

            default:
                // Evento não tratado
                return [
                    'success' => true,
                    'message' => 'Evento não processado: ' . $type
                ];
        }
    }

    /**
     * Trata sucesso de pagamento
     *
     * @param object $paymentIntent Payment Intent
     * @return array Resultado
     */
    private static function handlePaymentSuccess($paymentIntent) {
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if ($orderId && class_exists('OrderService')) {
            OrderService::updatePayment($orderId, [
                'payment_intent_id' => $paymentIntent->id,
                'charge_id' => $paymentIntent->charges->data[0]->id ?? null,
                'payment_status' => 'paid',
                'paid_at' => date('Y-m-d H:i:s', $paymentIntent->created)
            ]);

            // Atualiza status do pedido
            OrderService::updateOrderStatus($orderId, 'paid');
        }

        return [
            'success' => true,
            'message' => 'Pagamento confirmado',
            'order_id' => $orderId
        ];
    }

    /**
     * Trata falha de pagamento
     *
     * @param object $paymentIntent Payment Intent
     * @return array Resultado
     */
    private static function handlePaymentFailure($paymentIntent) {
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if ($orderId && class_exists('OrderService')) {
            OrderService::updatePayment($orderId, [
                'payment_intent_id' => $paymentIntent->id,
                'payment_status' => 'failed'
            ]);

            $errorMessage = $paymentIntent->last_payment_error->message ?? 'Pagamento falhou';
            OrderService::updateOrderStatus($orderId, 'payment_failed', null, $errorMessage);
        }

        return [
            'success' => true,
            'message' => 'Falha de pagamento registrada',
            'order_id' => $orderId
        ];
    }

    /**
     * Trata reembolso
     *
     * @param object $charge Charge
     * @return array Resultado
     */
    private static function handleRefund($charge) {
        $paymentIntentId = $charge->payment_intent ?? null;

        // Busca pedido pelo payment_intent_id
        if ($paymentIntentId && class_exists('OrderService')) {
            $db = getDB();
            $stmt = $db->prepare("SELECT id FROM orders WHERE stripe_payment_intent_id = ? LIMIT 1");
            $stmt->execute([$paymentIntentId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($order) {
                OrderService::updateOrderStatus($order['id'], 'refunded');
            }
        }

        return [
            'success' => true,
            'message' => 'Reembolso processado'
        ];
    }

    /**
     * Trata checkout completado (Stripe Checkout)
     *
     * @param object $session Sessão
     * @return array Resultado
     */
    private static function handleCheckoutCompleted($session) {
        $orderId = $session->metadata->order_id ?? null;

        if ($orderId && class_exists('OrderService')) {
            OrderService::updatePayment($orderId, [
                'payment_intent_id' => $session->payment_intent,
                'payment_status' => 'paid',
                'paid_at' => date('Y-m-d H:i:s')
            ]);

            OrderService::updateOrderStatus($orderId, 'paid');
        }

        return [
            'success' => true,
            'message' => 'Checkout completado',
            'order_id' => $orderId
        ];
    }

    /**
     * Cria reembolso
     *
     * @param string $chargeId ID da cobrança
     * @param int|null $amount Valor a reembolsar (null = total)
     * @param string $reason Motivo do reembolso
     * @return array Resultado
     * @throws Exception Se falhar
     */
    public static function createRefund($chargeId, $amount = null, $reason = 'requested_by_customer') {
        self::init();

        if (!class_exists('\Stripe\Refund')) {
            throw new Exception('Stripe SDK não está instalado');
        }

        try {
            $refundData = [
                'charge' => $chargeId,
                'reason' => $reason
            ];

            if ($amount !== null) {
                $refundData['amount'] = $amount;
            }

            $refund = \Stripe\Refund::create($refundData);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'status' => $refund->status,
                'amount' => $refund->amount
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception("Erro ao criar reembolso: " . $e->getMessage());
        }
    }

    /**
     * Obtém chave pública do Stripe
     *
     * @return string Chave pública
     */
    public static function getPublishableKey() {
        self::init();
        return self::$publishableKey;
    }
}
