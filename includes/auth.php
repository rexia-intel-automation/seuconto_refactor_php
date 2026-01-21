<?php
/**
 * Gerenciamento de Sessão
 *
 * Este arquivo gerencia sessões de usuários autenticados
 */

// Inicia sessão se ainda não iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Carrega paths.php se url() não estiver disponível
if (!function_exists('url')) {
    require_once __DIR__ . '/../config/paths.php';
}

/**
 * Verifica se o usuário está autenticado
 *
 * @return bool True se autenticado
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

/**
 * Obtém dados do usuário logado
 *
 * @return array|null Dados do usuário ou null se não autenticado
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'],
        'phone' => $_SESSION['user_phone'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'user'
    ];
}

/**
 * Define dados do usuário na sessão
 *
 * @param array $user Dados do usuário
 */
function setUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'] ?? $user['name'] ?? '';
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_phone'] = $user['phone'] ?? '';
    $_SESSION['user_role'] = $user['role'] ?? 'user';
    $_SESSION['logged_in_at'] = time();
}

/**
 * Remove dados do usuário da sessão (logout)
 */
function clearUserSession() {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_phone']);
    unset($_SESSION['user_role']);
    unset($_SESSION['logged_in_at']);

    // Destroi a sessão completamente
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

/**
 * Redireciona para login se não autenticado
 *
 * @param string $redirectTo URL para redirecionar após login
 */
function requireAuth($redirectTo = '') {
    if (!isLoggedIn()) {
        $loginUrl = url('pages/auth/login.php');

        if ($redirectTo) {
            $loginUrl .= '?redirect=' . urlencode($redirectTo);
        }

        header('Location: ' . $loginUrl);
        exit;
    }
}

/**
 * Verifica se o usuário é admin
 *
 * @return bool True se admin
 */
function isAdmin() {
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'admin';
}

/**
 * Redireciona para página inicial se não for admin
 */
function requireAdmin() {
    requireAuth();

    if (!isAdmin()) {
        header('Location: ' . url('pages/dashboard.php'));
        exit;
    }
}

/**
 * Gera token CSRF
 *
 * @return string Token CSRF
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida token CSRF
 *
 * @param string $token Token a validar
 * @return bool True se válido
 */
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Define mensagem flash
 *
 * @param string $message Mensagem
 * @param string $type Tipo: success, error, warning, info
 */
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Obtém e limpa mensagem flash
 *
 * @return array|null Mensagem flash ou null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}
