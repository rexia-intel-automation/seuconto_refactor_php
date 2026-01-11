<?php
/**
 * Middleware de Proteção de Rotas Administrativas
 *
 * Este arquivo deve ser incluído no início de todas as páginas administrativas
 * para garantir que apenas usuários com role adequado possam acessar
 */

// Carrega dependências se ainda não carregadas
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/auth.php';
}

if (!function_exists('hasRole')) {
    require_once __DIR__ . '/../config/permissions.php';
}

/**
 * Protege rota administrativa
 * Redireciona para login se não autenticado ou para dashboard se não for admin
 *
 * @param string $requiredRole Role mínimo necessário (padrão: admin)
 */
function protectAdminRoute($requiredRole = 'admin') {
    // Verifica se está autenticado
    if (!isLoggedIn()) {
        // Salva URL atual para redirecionar após login
        $currentUrl = $_SERVER['REQUEST_URI'] ?? '';
        $loginUrl = defined('BASE_PATH') ? BASE_PATH . '/pages/admin/login.php' : '/pages/admin/login.php';

        if ($currentUrl) {
            $loginUrl .= '?redirect=' . urlencode($currentUrl);
        }

        header('Location: ' . $loginUrl);
        exit;
    }

    // Verifica se tem o role necessário
    if (!hasRole($requiredRole)) {
        // Se for usuário comum, redireciona para dashboard
        $currentUser = getCurrentUser();
        $userRole = $currentUser['role'] ?? 'user';

        if ($userRole === 'user') {
            $dashboardUrl = defined('BASE_PATH') ? BASE_PATH . '/pages/dashboard.php' : '/pages/dashboard.php';
            setFlashMessage('Você não tem permissão para acessar esta área', 'error');
            header('Location: ' . $dashboardUrl);
            exit;
        } else {
            // Se for outro tipo de usuário sem permissão, retorna 403
            http_response_code(403);
            die('
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Acesso Negado</title>
                    <style>
                        body { font-family: sans-serif; text-align: center; padding: 50px; background: #f5f5f5; }
                        .error-box { background: white; padding: 40px; border-radius: 8px; max-width: 500px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                        h1 { color: #e74c3c; margin-bottom: 20px; }
                        p { color: #666; margin-bottom: 30px; }
                        a { display: inline-block; background: #3498db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; }
                        a:hover { background: #2980b9; }
                    </style>
                </head>
                <body>
                    <div class="error-box">
                        <h1>🚫 Acesso Negado</h1>
                        <p>Você não tem permissão para acessar esta área administrativa.</p>
                        <a href="/">Voltar para Home</a>
                    </div>
                </body>
                </html>
            ');
        }
    }
}

/**
 * Verifica se usuário pode executar uma ação específica
 *
 * @param string $permission Permissão necessária
 * @param bool $dieOnFail Se deve parar execução em caso de falha
 * @return bool True se tem permissão
 */
function canPerformAction($permission, $dieOnFail = true) {
    if (!hasPermission($permission)) {
        if ($dieOnFail) {
            http_response_code(403);

            // Se for requisição AJAX, retorna JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Você não tem permissão para realizar esta ação'
                ]);
                exit;
            }

            // Caso contrário, exibe página de erro
            die('
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Permissão Negada</title>
                    <style>
                        body { font-family: sans-serif; text-align: center; padding: 50px; background: #f5f5f5; }
                        .error-box { background: white; padding: 40px; border-radius: 8px; max-width: 500px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                        h1 { color: #e74c3c; margin-bottom: 20px; }
                        p { color: #666; margin-bottom: 30px; }
                        a { display: inline-block; background: #3498db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; }
                        a:hover { background: #2980b9; }
                    </style>
                </head>
                <body>
                    <div class="error-box">
                        <h1>⛔ Permissão Negada</h1>
                        <p>Você não tem permissão para realizar esta ação.</p>
                        <a href="javascript:history.back()">Voltar</a>
                    </div>
                </body>
                </html>
            ');
        }

        return false;
    }

    return true;
}

/**
 * Log de acesso administrativo
 * Registra acessos a áreas administrativas para auditoria
 *
 * @param string $action Ação realizada
 * @param array $metadata Dados adicionais
 */
function logAdminAccess($action, $metadata = []) {
    // Se o sistema de logs estiver configurado
    $logDir = __DIR__ . '/../logs';

    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }

    $logFile = $logDir . '/admin-access.log';
    $currentUser = getCurrentUser();

    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user_id' => $currentUser['id'] ?? null,
        'user_email' => $currentUser['email'] ?? null,
        'user_role' => $currentUser['role'] ?? 'unknown',
        'action' => $action,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'metadata' => $metadata
    ];

    $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . PHP_EOL;

    // Tenta escrever no log (silenciosamente falha se não conseguir)
    @file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
}

/**
 * Valida token de acesso administrativo
 * Útil para ações sensíveis que requerem confirmação adicional
 *
 * @param string $action Ação a validar
 * @return bool True se token é válido
 */
function validateAdminActionToken($action) {
    $token = $_POST['admin_token'] ?? $_GET['admin_token'] ?? '';

    if (empty($token)) {
        return false;
    }

    // Verifica se o token na sessão corresponde
    $expectedToken = $_SESSION['admin_action_token_' . $action] ?? '';

    if (empty($expectedToken)) {
        return false;
    }

    // Verifica timestamp (token expira em 30 minutos)
    $tokenData = explode('|', $expectedToken);
    if (count($tokenData) !== 2) {
        return false;
    }

    [$storedToken, $timestamp] = $tokenData;

    if (time() - (int)$timestamp > 1800) {
        // Token expirado
        unset($_SESSION['admin_action_token_' . $action]);
        return false;
    }

    // Valida o token
    if (!hash_equals($storedToken, $token)) {
        return false;
    }

    // Remove token usado (single-use)
    unset($_SESSION['admin_action_token_' . $action]);

    return true;
}

/**
 * Gera token para ação administrativa
 *
 * @param string $action Ação a gerar token
 * @return string Token gerado
 */
function generateAdminActionToken($action) {
    $token = bin2hex(random_bytes(32));
    $_SESSION['admin_action_token_' . $action] = $token . '|' . time();
    return $token;
}

// Proteção automática se este arquivo for incluído diretamente em páginas admin
// (apenas se não estiver sendo usado em um contexto de API)
if (strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/') !== false &&
    strpos($_SERVER['REQUEST_URI'] ?? '', '/api/admin/') === false &&
    basename($_SERVER['PHP_SELF']) !== 'admin-middleware.php') {

    // Aplica proteção automática
    protectAdminRoute();

    // Loga o acesso
    logAdminAccess('page_view', [
        'page' => $_SERVER['REQUEST_URI'] ?? '',
        'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET'
    ]);
}
