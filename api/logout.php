<?php
/**
 * API de Logout
 * Encerra sessão do usuário (comum ou admin)
 */

require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Verifica se está logado
if (!isLoggedIn()) {
    header('Location: ' . url('index.php'));
    exit;
}

// Salva role antes de limpar sessão (para redirecionar corretamente)
$wasAdmin = isAdmin();

// Limpa sessão
clearUserSession();

// Redireciona baseado no tipo de usuário
if ($wasAdmin) {
    header('Location: ' . url('pages/admin/login.php?logged_out=1'));
} else {
    header('Location: ' . url('index.php?logged_out=1'));
}
exit;
