<?php
/**
 * Configuração de Caminhos (Paths)
 *
 * Define constantes para paths absolutos e URLs base
 * para garantir que os links funcionem corretamente
 * independente do ambiente
 */

// Define o diretório raiz do projeto (se não estiver definido)
if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', dirname(__DIR__));
}

// Define o caminho base da URL
// Para desenvolvimento local em subpasta: '/refactor'
// Para produção na raiz: ''
// Para detectar automaticamente, use a função abaixo

/**
 * Detecta automaticamente o caminho base da URL
 */
function getBasePath() {
    // Se estiver usando variável de ambiente
    if (getenv('BASE_PATH')) {
        return getenv('BASE_PATH');
    }

    // Se estiver em variável .env
    if (defined('BASE_PATH_ENV')) {
        return BASE_PATH_ENV;
    }

    // Detecta baseado na diferença entre ROOT_DIR e DOCUMENT_ROOT
    $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $rootDir = realpath(ROOT_DIR);

    if ($documentRoot && $rootDir && strpos($rootDir, $documentRoot) === 0) {
        $basePath = substr($rootDir, strlen($documentRoot));
        // Normaliza barras para URL (sempre /)
        $basePath = str_replace('\\', '/', $basePath);
        return $basePath ?: '';
    }

    // Fallback: extrai do SCRIPT_NAME procurando diretórios conhecidos do projeto
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

    // Procura por marcadores conhecidos da estrutura do projeto
    $markers = ['/pages/', '/api/', '/assets/', '/components/', '/config/'];
    foreach ($markers as $marker) {
        $pos = strpos($scriptName, $marker);
        if ($pos !== false) {
            $basePath = substr($scriptName, 0, $pos);
            return $basePath ?: '';
        }
    }

    // Último fallback: se for index.php na raiz
    if (basename($scriptName) === 'index.php') {
        $basePath = dirname($scriptName);
        if ($basePath === '/' || $basePath === '\\') {
            return '';
        }
        return rtrim($basePath, '/');
    }

    return '';
}

// Define a constante BASE_PATH
if (!defined('BASE_PATH')) {
    define('BASE_PATH', getBasePath());
}

// Define a URL base completa
if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define('BASE_URL', $protocol . '://' . $host . BASE_PATH);
}

/**
 * Função auxiliar para gerar URLs
 *
 * @param string $path Caminho relativo (ex: 'pages/criar.php')
 * @return string URL completa
 */
function url($path = '') {
    $path = ltrim($path, '/');
    return BASE_PATH . ($path ? '/' . $path : '');
}

/**
 * Função auxiliar para gerar URLs de assets
 *
 * @param string $path Caminho do asset (ex: 'css/main.css')
 * @return string URL completa do asset
 */
function asset($path) {
    $path = ltrim($path, '/');
    return BASE_PATH . '/assets/' . $path;
}

/**
 * Redireciona para uma URL
 *
 * @param string $path Caminho relativo
 * @param int $statusCode Código HTTP (302 por padrão)
 */
function redirectTo($path, $statusCode = 302) {
    header('Location: ' . url($path), true, $statusCode);
    exit;
}
