<?php
/**
 * Login Administrativo - Área Restrita
 * Autenticação separada para administradores
 */

$pageTitle = 'Admin Login - Seu Conto';
$pageDescription = 'Acesso restrito à área administrativa';
$additionalCSS = ['/refactor/assets/css/auth.css'];

// Carrega dependências
require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/permissions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

// Redireciona se já está logado como admin
if (isLoggedIn() && isAdmin()) {
    header('Location: ' . url('pages/admin/index.php'));
    exit;
}

// Redireciona usuário comum para área pública
if (isLoggedIn() && !isAdmin()) {
    setFlashMessage('Você não tem permissão para acessar a área administrativa', 'error');
    header('Location: ' . url('pages/dashboard.php'));
    exit;
}

// Inclui head + header simplificado (sem menu)
require_once __DIR__ . '/../../components/head.php';
?>

<!-- Admin Login Container -->
<div class="auth-container" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--color-background) 0%, var(--color-muted) 100%);">
    <div class="auth-card" style="max-width: 450px; width: 100%; margin: 2rem;">
        <!-- Header -->
        <div class="auth-header" style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 80px; height: 80px; background: var(--color-primary); border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <h1 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem;">Área Administrativa</h1>
            <p class="text-muted">Acesso restrito a administradores</p>
        </div>

        <!-- Alert de Mensagens -->
        <div id="form-alert"></div>

        <!-- Formulário de Login -->
        <form id="admin-login-form" class="auth-form">
            <div class="form-group">
                <label class="form-label" for="email">Email do Administrador</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="admin@seuconto.com.br" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Senha</label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
                    <button type="button" id="toggle-password" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--color-muted-foreground);">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full btn-lg" id="submit-btn">
                Entrar no Admin
            </button>
        </form>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--color-border);">
            <p class="text-muted" style="font-size: 0.875rem;">
                <a href="<?php echo url('index.php'); ?>" style="color: var(--color-primary); text-decoration: none;">
                    ← Voltar para o site
                </a>
            </p>
        </div>
    </div>
</div>

<script>
// Toggle mostrar senha
document.getElementById('toggle-password').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
});

// Submit do formulário
document.getElementById('admin-login-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submit-btn');
    const alertDiv = document.getElementById('form-alert');

    // Desabilita botão
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner"></span> Verificando...';

    // Limpa alert anterior
    alertDiv.innerHTML = '';

    const formData = new FormData(this);
    formData.append('is_admin', 'true'); // Flag para identificar login admin

    try {
        const response = await fetch('<?php echo url('api/auth.php'); ?>', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            // Sucesso - redireciona para admin
            alertDiv.innerHTML = `
                <div class="auth-alert auth-alert-success">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Login realizado! Redirecionando...
                </div>
            `;

            setTimeout(() => {
                window.location.href = '<?php echo url('pages/admin/index.php'); ?>';
            }, 1000);
        } else {
            throw new Error(result.message || 'Erro ao fazer login');
        }
    } catch (error) {
        alertDiv.innerHTML = `
            <div class="auth-alert auth-alert-error">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
                ${error.message}
            </div>
        `;

        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Entrar no Admin';
    }
});

// Detecta tentativas de força bruta
let loginAttempts = 0;
const maxAttempts = <?php echo MAX_LOGIN_ATTEMPTS; ?>;

document.getElementById('admin-login-form').addEventListener('submit', function() {
    loginAttempts++;

    if (loginAttempts >= maxAttempts) {
        alert('Muitas tentativas de login. Aguarde alguns minutos.');
        document.getElementById('submit-btn').disabled = true;

        setTimeout(() => {
            loginAttempts = 0;
            document.getElementById('submit-btn').disabled = false;
        }, <?php echo LOGIN_LOCKOUT_TIME * 1000; ?>);
    }
});
</script>

<style>
.spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.auth-container {
    padding: 2rem 1rem;
}

.auth-card {
    background: var(--color-card);
    border-radius: var(--radius-xl);
    padding: 3rem;
    box-shadow: var(--shadow-xl);
}

.auth-form {
    margin-top: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--color-border);
    border-radius: var(--radius);
    font-size: 1rem;
    transition: all var(--transition-base);
}

.form-input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.auth-alert {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: var(--radius);
    margin-bottom: 1.5rem;
    font-size: 0.875rem;
}

.auth-alert-success {
    background: rgba(34, 197, 94, 0.1);
    color: var(--color-success);
    border: 1px solid var(--color-success);
}

.auth-alert-error {
    background: rgba(239, 68, 68, 0.1);
    color: var(--color-error);
    border: 1px solid var(--color-error);
}

@media (max-width: 480px) {
    .auth-card {
        padding: 2rem 1.5rem;
    }
}
</style>

</body>
</html>
