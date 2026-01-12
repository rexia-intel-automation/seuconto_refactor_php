<?php
/**
 * Bootstrap - Inicialização Centralizada
 *
 * Este arquivo carrega todas as dependências necessárias da aplicação
 * em uma única inclusão. Use em todas as páginas para garantir
 * consistência e evitar includes duplicados.
 *
 * Ordem de carregamento:
 * 1. env.php - Variáveis de ambiente
 * 2. paths.php - Constantes de caminho e funções url()/asset()
 * 3. config.php - Configurações da aplicação
 * 4. db.php - Conexão com banco de dados
 * 5. functions.php - Funções utilitárias
 * 6. auth.php - Gerenciamento de sessão
 *
 * Uso:
 *   require_once __DIR__ . '/config/bootstrap.php';
 */

// Evita carregamento duplicado
if (defined('BOOTSTRAP_LOADED')) {
    return;
}
define('BOOTSTRAP_LOADED', true);

// Define diretório raiz se ainda não definido
if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', dirname(__DIR__));
}

// 1. Carrega variáveis de ambiente (.env)
require_once __DIR__ . '/env.php';

// 2. Carrega configuração de caminhos (BASE_PATH, BASE_URL, url(), asset())
require_once __DIR__ . '/paths.php';

// 3. Carrega configurações da aplicação
require_once __DIR__ . '/config.php';

// 4. Carrega conexão com banco de dados
require_once __DIR__ . '/db.php';

// 5. Carrega funções utilitárias
require_once ROOT_DIR . '/includes/functions.php';

// 6. Carrega gerenciamento de sessão/autenticação
require_once ROOT_DIR . '/includes/auth.php';

/**
 * Autoloader simples para Services
 *
 * Carrega classes de services automaticamente quando necessário
 */
spl_autoload_register(function ($class) {
    // Só carrega classes que terminam com "Service"
    if (substr($class, -7) !== 'Service') {
        return;
    }

    $file = ROOT_DIR . '/services/' . $class . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
