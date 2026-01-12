<?php
/**
 * Configurações do Sistema
 *
 * Permite aos administradores configurar parâmetros do sistema
 */

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/permissions.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/admin-middleware.php';

// Proteger rota - apenas super admins
protectAdminRoute('super_admin');

// Processar formulário de atualização
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $action = $_POST['action'];

        switch ($action) {
            case 'update_prices':
                // Atualizar preços (em produção, isso iria para um arquivo de config ou banco)
                $successMessage = 'Preços atualizados com sucesso! (Funcionalidade demonstrativa)';
                break;

            case 'update_smtp':
                // Atualizar configurações SMTP
                $successMessage = 'Configurações de email atualizadas com sucesso! (Funcionalidade demonstrativa)';
                break;

            case 'update_stripe':
                // Atualizar configurações Stripe
                $successMessage = 'Configurações de pagamento atualizadas com sucesso! (Funcionalidade demonstrativa)';
                break;

            case 'update_n8n':
                // Atualizar configurações n8n
                $successMessage = 'Configurações de IA atualizadas com sucesso! (Funcionalidade demonstrativa)';
                break;

            default:
                $errorMessage = 'Ação inválida';
        }
    } catch (Exception $e) {
        error_log("Erro ao atualizar configurações: " . $e->getMessage());
        $errorMessage = 'Erro ao atualizar configurações: ' . $e->getMessage();
    }
}

$pageTitle = 'Configurações';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?> - Seu Conto Admin</title>

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo url('assets/css/main.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('assets/css/admin.css'); ?>">
</head>
<body class="admin-layout">

    <?php include __DIR__ . '/../../../components/admin/sidebar.php'; ?>

    <div class="admin-main">

        <?php include __DIR__ . '/../../../components/admin/topbar.php'; ?>

        <div class="admin-content">

            <!-- Header -->
            <div class="page-header">
                <div>
                    <h1>Configurações</h1>
                    <p class="text-muted">Gerencie as configurações do sistema</p>
                </div>
            </div>

            <!-- Messages -->
            <?php if ($successMessage): ?>
                <div class="alert alert-success">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <?php echo e($successMessage); ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMessage): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <?php echo e($errorMessage); ?>
                </div>
            <?php endif; ?>

            <!-- Settings Tabs -->
            <div class="settings-container">

                <!-- Sidebar Navigation -->
                <div class="settings-sidebar">
                    <nav class="settings-nav">
                        <a href="#prices" class="settings-nav-item active">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                            Preços
                        </a>
                        <a href="#email" class="settings-nav-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                            Email (SMTP)
                        </a>
                        <a href="#payment" class="settings-nav-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                <line x1="1" y1="10" x2="23" y2="10"/>
                            </svg>
                            Pagamentos (Stripe)
                        </a>
                        <a href="#ai" class="settings-nav-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                            IA e n8n
                        </a>
                        <a href="#general" class="settings-nav-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"/>
                            </svg>
                            Geral
                        </a>
                    </nav>
                </div>

                <!-- Settings Content -->
                <div class="settings-content">

                    <!-- Preços -->
                    <section id="prices" class="settings-section">
                        <div class="card">
                            <div class="card-header">
                                <h3>Configurações de Preços</h3>
                            </div>
                            <div class="card-body">
                                <form method="POST" class="settings-form">
                                    <input type="hidden" name="action" value="update_prices">

                                    <div class="form-group">
                                        <label>Preço do E-book (em centavos)</label>
                                        <input
                                            type="number"
                                            name="price_ebook"
                                            value="<?php echo PRICE_EBOOK; ?>"
                                            class="form-input"
                                            step="1"
                                            min="0"
                                        >
                                        <small class="form-hint">
                                            Valor atual: <?php echo formatPrice(PRICE_EBOOK); ?>
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label>Preço do Livro Físico (em centavos)</label>
                                        <input
                                            type="number"
                                            name="price_physical"
                                            value="<?php echo PRICE_PHYSICAL; ?>"
                                            class="form-input"
                                            step="1"
                                            min="0"
                                        >
                                        <small class="form-hint">
                                            Valor atual: <?php echo formatPrice(PRICE_PHYSICAL); ?>
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label>Percentual de Doação (%)</label>
                                        <input
                                            type="number"
                                            name="donation_percentage"
                                            value="<?php echo DONATION_PERCENTAGE; ?>"
                                            class="form-input"
                                            step="1"
                                            min="0"
                                            max="100"
                                        >
                                        <small class="form-hint">
                                            Percentual doado para ONGs por cada venda
                                        </small>
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">
                                            Salvar Alterações
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>

                    <!-- Email (SMTP) -->
                    <section id="email" class="settings-section" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h3>Configurações de Email (SMTP)</h3>
                            </div>
                            <div class="card-body">
                                <form method="POST" class="settings-form">
                                    <input type="hidden" name="action" value="update_smtp">

                                    <div class="form-group">
                                        <label>Host SMTP</label>
                                        <input
                                            type="text"
                                            name="smtp_host"
                                            value="<?php echo e(env('SMTP_HOST', '')); ?>"
                                            class="form-input"
                                            placeholder="smtp.example.com"
                                        >
                                    </div>

                                    <div class="form-group">
                                        <label>Porta SMTP</label>
                                        <input
                                            type="number"
                                            name="smtp_port"
                                            value="<?php echo e(env('SMTP_PORT', '587')); ?>"
                                            class="form-input"
                                        >
                                    </div>

                                    <div class="form-group">
                                        <label>Usuário SMTP</label>
                                        <input
                                            type="text"
                                            name="smtp_user"
                                            value="<?php echo e(env('SMTP_USER', '')); ?>"
                                            class="form-input"
                                        >
                                    </div>

                                    <div class="form-group">
                                        <label>Senha SMTP</label>
                                        <input
                                            type="password"
                                            name="smtp_password"
                                            placeholder="••••••••"
                                            class="form-input"
                                        >
                                        <small class="form-hint">
                                            Deixe em branco para manter a senha atual
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label>Email de Envio</label>
                                        <input
                                            type="email"
                                            name="smtp_from"
                                            value="<?php echo e(env('SMTP_FROM', '')); ?>"
                                            class="form-input"
                                            placeholder="noreply@seuconto.com"
                                        >
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">
                                            Salvar Alterações
                                        </button>
                                        <button type="button" class="btn btn-outline" onclick="testEmail()">
                                            Enviar Email de Teste
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>

                    <!-- Pagamentos (Stripe) -->
                    <section id="payment" class="settings-section" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h3>Configurações de Pagamento (Stripe)</h3>
                            </div>
                            <div class="card-body">
                                <form method="POST" class="settings-form">
                                    <input type="hidden" name="action" value="update_stripe">

                                    <div class="form-group">
                                        <label>Chave Publicável (Publishable Key)</label>
                                        <input
                                            type="text"
                                            name="stripe_publishable_key"
                                            value="<?php echo e(env('STRIPE_PUBLISHABLE_KEY', '')); ?>"
                                            class="form-input"
                                            placeholder="pk_test_..."
                                        >
                                    </div>

                                    <div class="form-group">
                                        <label>Chave Secreta (Secret Key)</label>
                                        <input
                                            type="password"
                                            name="stripe_secret_key"
                                            placeholder="sk_test_..."
                                            class="form-input"
                                        >
                                        <small class="form-hint">
                                            Deixe em branco para manter a chave atual
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label>Webhook Secret</label>
                                        <input
                                            type="password"
                                            name="stripe_webhook_secret"
                                            placeholder="whsec_..."
                                            class="form-input"
                                        >
                                        <small class="form-hint">
                                            Secret para validação de webhooks do Stripe
                                        </small>
                                    </div>

                                    <div class="alert alert-info">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="16" x2="12" y2="12"/>
                                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                                        </svg>
                                        <div>
                                            <strong>Webhook URL:</strong><br>
                                            <code><?php echo url('api/stripe-webhook.php'); ?></code><br>
                                            Configure esta URL no painel do Stripe
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">
                                            Salvar Alterações
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>

                    <!-- IA e n8n -->
                    <section id="ai" class="settings-section" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h3>Configurações de IA e n8n</h3>
                            </div>
                            <div class="card-body">
                                <form method="POST" class="settings-form">
                                    <input type="hidden" name="action" value="update_n8n">

                                    <div class="form-group">
                                        <label>URL do Webhook n8n</label>
                                        <input
                                            type="url"
                                            name="n8n_webhook_url"
                                            value="<?php echo e(env('N8N_WEBHOOK_URL', '')); ?>"
                                            class="form-input"
                                            placeholder="https://n8n.example.com/webhook/..."
                                        >
                                        <small class="form-hint">
                                            URL do webhook que dispara a geração de livros
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label>API Key (opcional)</label>
                                        <input
                                            type="password"
                                            name="n8n_api_key"
                                            placeholder="••••••••"
                                            class="form-input"
                                        >
                                        <small class="form-hint">
                                            Chave de autenticação para o n8n (se necessário)
                                        </small>
                                    </div>

                                    <div class="alert alert-info">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="16" x2="12" y2="12"/>
                                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                                        </svg>
                                        <div>
                                            <strong>Callback URL:</strong><br>
                                            <code><?php echo url('api/n8n-callback.php'); ?></code><br>
                                            Configure esta URL no n8n para receber atualizações
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">
                                            Salvar Alterações
                                        </button>
                                        <button type="button" class="btn btn-outline" onclick="testN8nConnection()">
                                            Testar Conexão
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>

                    <!-- Geral -->
                    <section id="general" class="settings-section" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h3>Configurações Gerais</h3>
                            </div>
                            <div class="card-body">
                                <div class="info-grid">
                                    <div class="info-item">
                                        <label>Versão do Sistema</label>
                                        <div class="info-value">1.0.0</div>
                                    </div>
                                    <div class="info-item">
                                        <label>Ambiente</label>
                                        <div class="info-value">
                                            <?php echo env('APP_ENV', 'production') === 'development' ? 'Desenvolvimento' : 'Produção'; ?>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <label>Base URL</label>
                                        <div class="info-value">
                                            <code><?php echo BASE_URL; ?></code>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <label>Timezone</label>
                                        <div class="info-value">America/Sao_Paulo</div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="button" class="btn btn-outline" onclick="clearCache()">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="1 4 1 10 7 10"/><polyline points="23 20 23 14 17 14"/>
                                            <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
                                        </svg>
                                        Limpar Cache
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>

            </div>

        </div>

    </div>

    <!-- Scripts -->
    <script>
        // Tab navigation
        document.querySelectorAll('.settings-nav-item').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();

                // Remove active class from all
                document.querySelectorAll('.settings-nav-item').forEach(item => {
                    item.classList.remove('active');
                });
                document.querySelectorAll('.settings-section').forEach(section => {
                    section.style.display = 'none';
                });

                // Add active to clicked
                link.classList.add('active');
                const targetId = link.getAttribute('href').substring(1);
                document.getElementById(targetId).style.display = 'block';
            });
        });

        // Test email
        async function testEmail() {
            const email = prompt('Digite o email de destino para o teste:');
            if (!email) return;

            try {
                const response = await fetch('<?php echo url('api/admin/test-email.php'); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });

                const result = await response.json();
                alert(result.success ? 'Email enviado com sucesso!' : 'Erro: ' + result.error);
            } catch (error) {
                alert('Erro ao enviar email de teste');
            }
        }

        // Test n8n connection
        async function testN8nConnection() {
            try {
                const response = await fetch('<?php echo url('api/admin/test-n8n.php'); ?>');
                const result = await response.json();
                alert(result.success ? 'Conexão OK!' : 'Erro: ' + result.error);
            } catch (error) {
                alert('Erro ao testar conexão com n8n');
            }
        }

        // Clear cache
        function clearCache() {
            if (confirm('Deseja limpar o cache do sistema?')) {
                alert('Cache limpo com sucesso! (Funcionalidade demonstrativa)');
            }
        }
    </script>

</body>
</html>
