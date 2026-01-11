<?php
/**
 * Carregador de Variáveis de Ambiente
 *
 * Este arquivo carrega as variáveis de ambiente do arquivo .env
 * Similar ao dotenv do Node.js
 */

/**
 * Carrega variáveis de ambiente do arquivo .env
 *
 * @param string $path Caminho para o arquivo .env
 * @return void
 */
function loadEnv($path = __DIR__ . '/.env') {
    if (!file_exists($path)) {
        throw new Exception("Arquivo .env não encontrado em: {$path}");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Ignora comentários e linhas vazias
        if (strpos(trim($line), '#') === 0 || trim($line) === '') {
            continue;
        }

        // Parse da linha
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove aspas se existirem
            $value = trim($value, '"\'');

            // Define a variável de ambiente
            if (!array_key_exists($name, $_ENV)) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

/**
 * Obtém uma variável de ambiente com valor padrão opcional
 *
 * @param string $key Nome da variável
 * @param mixed $default Valor padrão se não existir
 * @return mixed
 */
function env($key, $default = null) {
    $value = getenv($key);

    if ($value === false) {
        return $default;
    }

    // Converte valores booleanos
    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'null':
        case '(null)':
            return null;
        case 'empty':
        case '(empty)':
            return '';
    }

    return $value;
}

// Carrega as variáveis automaticamente quando o arquivo é incluído
loadEnv();

// ============================================
// EXEMPLO DE VARIÁVEIS ESPERADAS NO .env
// ============================================
/*
# Ambiente da Aplicação
APP_ENV=development
DEBUG=true
BASE_PATH=/refactor

# Banco de Dados
DB_HOST=localhost
DB_NAME=seuconto
DB_USER=root
DB_PASSWORD=
DB_CHARSET=utf8mb4

# Stripe (Pagamentos)
STRIPE_PUBLISHABLE_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_EBOOK_PRICE_ID=price_...
STRIPE_COLORING_BOOK_PRICE_ID=price_...

# Preços (em centavos)
PRICE_EBOOK=2990
PRICE_COLORING_BOOK=990

# n8n (Automação e IA)
N8N_WEBHOOK_URL=https://n8n.seudominio.com/webhook/generate-book
N8N_WEBHOOK_SECRET=seu_secret_aleatorio_aqui
N8N_STATUS_CALLBACK_URL=https://seusite.com/api/n8n-callback.php

# Email (SMTP)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=seu-email@gmail.com
SMTP_PASSWORD=sua-senha-app
SMTP_FROM_EMAIL=noreply@seuconto.com.br
SMTP_FROM_NAME=Seu Conto

# URLs
SITE_URL=https://seuconto.com.br
*/
