<?php
/**
 * Configuração do Stripe
 *
 * Este arquivo gerencia a integração com o Stripe para processamento de pagamentos
 * Requer a biblioteca Stripe PHP: composer require stripe/stripe-php
 */

require_once __DIR__ . '/env.php';

// Carrega a biblioteca do Stripe (assumindo instalação via Composer)
// Se não tiver Composer, baixe de: https://github.com/stripe/stripe-php/releases
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

/**
 * Configuração do Stripe
 */
class StripeConfig {
    private static $initialized = false;

    /**
     * Inicializa o Stripe com a chave secreta
     */
    public static function init() {
        if (self::$initialized) {
            return;
        }

        $secretKey = env('STRIPE_SECRET_KEY');

        if (!$secretKey) {
            throw new Exception('STRIPE_SECRET_KEY não configurada no arquivo .env');
        }

        // Verifica se a classe Stripe existe (biblioteca instalada)
        if (class_exists('\Stripe\Stripe')) {
            \Stripe\Stripe::setApiKey($secretKey);
            self::$initialized = true;
        } else {
            throw new Exception('Biblioteca Stripe PHP não instalada. Execute: composer require stripe/stripe-php');
        }
    }

    /**
     * Obtém a chave pública do Stripe
     *
     * @return string
     */
    public static function getPublishableKey() {
        return env('STRIPE_PUBLISHABLE_KEY', '');
    }

    /**
     * Obtém o ID do preço do e-book
     *
     * @return string
     */
    public static function getEbookPriceId() {
        return env('STRIPE_EBOOK_PRICE_ID', '');
    }

    /**
     * Obtém o ID do preço do livro de colorir
     *
     * @return string
     */
    public static function getColoringBookPriceId() {
        return env('STRIPE_COLORING_BOOK_PRICE_ID', '');
    }

    /**
     * Cria uma sessão de checkout do Stripe
     *
     * @param array $params Parâmetros da sessão
     * @return \Stripe\Checkout\Session
     */
    public static function createCheckoutSession($params) {
        self::init();

        try {
            return \Stripe\Checkout\Session::create($params);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            error_log("Erro ao criar sessão Stripe: " . $e->getMessage());
            throw new Exception("Erro ao processar pagamento. Por favor, tente novamente.");
        }
    }

    /**
     * Recupera uma sessão de checkout
     *
     * @param string $sessionId ID da sessão
     * @return \Stripe\Checkout\Session
     */
    public static function getCheckoutSession($sessionId) {
        self::init();

        try {
            return \Stripe\Checkout\Session::retrieve($sessionId);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            error_log("Erro ao recuperar sessão Stripe: " . $e->getMessage());
            throw new Exception("Erro ao verificar status do pagamento.");
        }
    }

    /**
     * Verifica a assinatura do webhook
     *
     * @param string $payload Corpo da requisição
     * @param string $signature Assinatura do header
     * @return \Stripe\Event
     */
    public static function constructWebhookEvent($payload, $signature) {
        self::init();

        $webhookSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            return \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                $webhookSecret
            );
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            error_log("Erro de verificação de assinatura webhook: " . $e->getMessage());
            throw new Exception("Webhook inválido");
        }
    }
}

// Nota: Constantes de preço e função formatPrice()
// foram movidas para config/config.php para centralização
