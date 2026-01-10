<?php
$pageTitle = 'Criar Conta - Seu Conto';
$additionalCSS = ['/refactor/assets/css/auth.css'];
$additionalJS = ['/refactor/assets/js/auth.js'];

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

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
            <h1 class="auth-title">Crie sua conta</h1>
            <p class="auth-subtitle">E comece a criar histórias mágicas</p>
        </div>

        <div id="form-alert"></div>

        <form id="register-form" class="auth-form">
            <div class="form-group">
                <label class="form-label" for="fullName">Nome Completo</label>
                <input type="text" id="fullName" name="fullName" class="form-input" placeholder="João da Silva" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="seu@email.com" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">WhatsApp/Telefone</label>
                <input type="tel" id="phone" name="phone" class="form-input" placeholder="(11) 99999-9999" required>
                <div class="whatsapp-hint">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Usaremos para enviar seu livro
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Senha</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Mínimo 6 caracteres" required>
                <div class="password-strength">
                    <div class="strength-bar"><div class="strength-fill"></div></div>
                    <p class="strength-text"></p>
                </div>
            </div>

            <div class="terms-checkbox">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">Aceito os <a href="/refactor/pages/termos.php" target="_blank">Termos de Uso</a> e <a href="/refactor/pages/privacidade.php" target="_blank">Política de Privacidade</a></label>
            </div>

            <button type="submit" class="auth-submit">Criar Conta</button>
        </form>

        <div class="auth-toggle">
            <p>Já tem uma conta? <a href="/refactor/pages/auth/login.php">Entrar</a></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
