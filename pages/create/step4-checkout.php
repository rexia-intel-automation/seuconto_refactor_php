<?php
/**
 * Wizard de Criação - Passo 4: Checkout e Pagamento
 */

// Carrega dependências
require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/stripe.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../services/OrderService.php';
require_once __DIR__ . '/../../services/PaymentService.php';

$pageTitle = 'Finalizar Pedido - Seu Conto';
$pageDescription = 'Complete seu pedido e receba o livro mágico';
$additionalCSS = [asset('css/wizard.css')];

// Requer autenticação
requireAuth();
$currentUser = getCurrentUser();

// Obtém order_id
$orderId = $_GET['order_id'] ?? null;
if (!$orderId) {
    header('Location: ' . url('pages/create/step1-theme.php'));
    exit;
}

// Busca dados do pedido
try {
    $order = OrderService::getOrder($orderId);

    if (!$order) {
        throw new Exception('Pedido não encontrado');
    }

    // Verifica se é do usuário logado
    if ($order['user_id'] != $currentUser['id']) {
        throw new Exception('Acesso negado');
    }

} catch (Exception $e) {
    setFlashMessage('Pedido não encontrado', 'error');
    header('Location: ' . url('pages/dashboard.php'));
    exit;
}

// Obtém chave pública do Stripe
$stripePublishableKey = PaymentService::getPublishableKey();

// Inclui head + header
require_once __DIR__ . '/../../components/head.php';
require_once __DIR__ . '/../../components/header.php';
?>

<!-- Wizard Container -->
<div class="wizard-container">
    <div class="container" style="max-width: 1000px;">
        <!-- Indicador de Progresso -->
        <div class="wizard-progress">
            <div class="wizard-step completed">
                <div class="wizard-step-number">✓</div>
                <span class="wizard-step-label">Tema</span>
            </div>
            <div class="wizard-step-line"></div>
            <div class="wizard-step completed">
                <div class="wizard-step-number">✓</div>
                <span class="wizard-step-label">Foto & Dados</span>
            </div>
            <div class="wizard-step-line"></div>
            <div class="wizard-step completed">
                <div class="wizard-step-number">✓</div>
                <span class="wizard-step-label">Processando</span>
            </div>
            <div class="wizard-step-line"></div>
            <div class="wizard-step active">
                <div class="wizard-step-number">4</div>
                <span class="wizard-step-label">Checkout</span>
            </div>
        </div>

        <!-- Conteúdo do Passo -->
        <div class="wizard-content">
            <div class="wizard-header">
                <h1 class="gradient-text">🎉 Seu Livro Está Pronto!</h1>
                <p class="text-muted" style="font-size: 1.125rem; margin-top: 1rem;">
                    Complete o pagamento para fazer o download
                </p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 400px; gap: 3rem; margin-top: 3rem;">
                <!-- Coluna Esquerda: Resumo do Pedido -->
                <div>
                    <h3 style="margin-bottom: 1.5rem;">📖 Resumo do Pedido</h3>

                    <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
                        <div style="display: flex; gap: 1.5rem; margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid var(--color-border);">
                            <?php
                            $themeInfo = getThemeInfo($order['theme']);
                            ?>
                            <div style="width: 120px; height: 120px; background: <?php echo $themeInfo['color'] ?? 'var(--color-primary)'; ?>; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                            </div>
                            <div style="flex: 1;">
                                <h2 style="margin-bottom: 0.5rem;">A História de <?php echo e($order['child_name']); ?></h2>
                                <p class="text-muted" style="margin-bottom: 1rem;">
                                    Tema: <strong><?php echo e($themeInfo['name'] ?? ucfirst($order['theme'])); ?></strong>
                                </p>
                                <div style="display: flex; gap: 2rem; font-size: 0.875rem; color: var(--color-muted-foreground);">
                                    <span>👤 <?php echo $order['child_age']; ?> anos</span>
                                    <span>📅 <?php echo formatDate($order['created_at']); ?></span>
                                </div>
                            </div>
                        </div>

                        <h4 style="margin-bottom: 1rem;">O que está incluído:</h4>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid var(--color-border);">
                                <span style="color: var(--color-success); font-size: 1.25rem;">✓</span>
                                <span>E-book em PDF de alta qualidade</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid var(--color-border);">
                                <span style="color: var(--color-success); font-size: 1.25rem;">✓</span>
                                <span>12-15 ilustrações personalizadas</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid var(--color-border);">
                                <span style="color: var(--color-success); font-size: 1.25rem;">✓</span>
                                <span>História única criada por IA</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid var(--color-border);">
                                <span style="color: var(--color-success); font-size: 1.25rem;">✓</span>
                                <span>Dedicatória personalizada</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 0;">
                                <span style="color: var(--color-success); font-size: 1.25rem;">✓</span>
                                <span>Pronto para impressão ou leitura digital</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Garantias -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                        <div class="card" style="padding: 1rem; text-align: center;">
                            <div style="font-size: 2rem; margin-bottom: 0.5rem;">🔒</div>
                            <p style="font-size: 0.875rem; font-weight: 600; margin: 0;">Pagamento Seguro</p>
                        </div>
                        <div class="card" style="padding: 1rem; text-align: center;">
                            <div style="font-size: 2rem; margin-bottom: 0.5rem;">⚡</div>
                            <p style="font-size: 0.875rem; font-weight: 600; margin: 0;">Download Imediato</p>
                        </div>
                        <div class="card" style="padding: 1rem; text-align: center;">
                            <div style="font-size: 2rem; margin-bottom: 0.5rem;">💯</div>
                            <p style="font-size: 0.875rem; font-weight: 600; margin: 0;">100% Único</p>
                        </div>
                    </div>
                </div>

                <!-- Coluna Direita: Checkout -->
                <div>
                    <div class="card" style="padding: 2rem; position: sticky; top: 100px;">
                        <h3 style="margin-bottom: 1.5rem;">💳 Pagamento</h3>

                        <!-- Preço -->
                        <div style="background: var(--color-muted); padding: 1.5rem; border-radius: var(--radius); margin-bottom: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem;">
                                <span>E-book Personalizado</span>
                                <span style="font-size: 1.25rem; font-weight: 700;"><?php echo formatPrice($order['price']); ?></span>
                            </div>
                            <p class="text-muted" style="font-size: 0.75rem; margin: 0;">Desconto de lançamento aplicado</p>
                        </div>

                        <!-- Botão de Pagamento -->
                        <button id="checkout-button" class="btn btn-primary btn-full btn-lg" style="margin-bottom: 1rem;">
                            Pagar Agora 🚀
                        </button>

                        <p class="text-muted" style="font-size: 0.75rem; text-align: center; margin: 0;">
                            Pagamento 100% seguro via Stripe
                        </p>

                        <!-- Logos de Pagamento -->
                        <div style="display: flex; justify-content: center; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--color-border); opacity: 0.6;">
                            <span style="font-size: 0.75rem;">💳 Cartão</span>
                            <span style="font-size: 0.75rem;">📱 Pix</span>
                            <span style="font-size: 0.75rem;">🏦 Boleto</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('<?php echo $stripePublishableKey; ?>');
const checkoutButton = document.getElementById('checkout-button');

checkoutButton.addEventListener('click', async () => {
    checkoutButton.disabled = true;
    checkoutButton.innerHTML = '<span class="spinner"></span> Processando...';

    try {
        // Cria sessão de checkout
        const response = await fetch('<?php echo url('api/create-checkout-session.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                order_id: '<?php echo $orderId; ?>'
            })
        });

        const result = await response.json();

        if (result.success) {
            // Redireciona para Stripe Checkout
            const { error } = await stripe.redirectToCheckout({
                sessionId: result.session_id
            });

            if (error) {
                throw new Error(error.message);
            }
        } else {
            throw new Error(result.message || 'Erro ao criar sessão de checkout');
        }
    } catch (error) {
        alert('Erro ao processar pagamento: ' + error.message);
        checkoutButton.disabled = false;
        checkoutButton.innerHTML = 'Pagar Agora 🚀';
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

@media (max-width: 968px) {
    .wizard-content > div {
        grid-template-columns: 1fr !important;
    }

    .card[style*="sticky"] {
        position: static !important;
    }
}
</style>

<?php require_once __DIR__ . '/../../components/footer.php'; ?>
