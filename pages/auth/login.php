<?php
$pageTitle = 'Login - Seu Conto';
$additionalCSS = ['/refactor/assets/css/auth.css'];
$additionalJS = ['/refactor/assets/js/auth.js'];

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

// Redireciona se já está logado
if (isLoggedIn()) {
    redirect('/refactor/pages/dashboard.php');
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
            </div>
            <h1 class="auth-title">Bem-vindo de volta!</h1>
            <p class="auth-subtitle">Entre para acessar seus livros</p>
        </div>

        <div id="form-alert"></div>

        <form id="login-form" class="auth-form">
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="seu@email.com" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Senha</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>

            <div class="auth-remember">
                <div class="checkbox-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Lembrar de mim</label>
                </div>
                <a href="#" class="forgot-link">Esqueceu a senha?</a>
            </div>

            <button type="submit" class="auth-submit">Entrar</button>
        </form>

        <div class="auth-toggle">
            <p>Não tem uma conta? <a href="/refactor/pages/auth/register.php">Criar conta</a></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
