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
