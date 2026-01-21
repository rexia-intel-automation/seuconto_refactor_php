<?php
/**
 * Wizard de Criação - Passo 3: Processamento
 * Tela de espera enquanto a IA gera o livro
 */

// Carrega dependências
require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

$pageTitle = 'Gerando seu Livro... | Seu Conto';
$pageDescription = 'Nossa IA está criando uma história mágica';
$additionalCSS = [asset('css/wizard.css')];

// Requer autenticação
requireAuth();
$currentUser = getCurrentUser();

// Inclui head + header
require_once __DIR__ . '/../../components/head.php';
require_once __DIR__ . '/../../components/header.php';

// Obtém order_id da query string (se vier de API)
$orderId = $_GET['order_id'] ?? null;
?>

<!-- Wizard Container -->
<div class="wizard-container">
    <div class="container" style="max-width: 800px;">
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
            <div class="wizard-step active">
                <div class="wizard-step-number">3</div>
                <span class="wizard-step-label">Processando</span>
            </div>
            <div class="wizard-step-line"></div>
            <div class="wizard-step">
                <div class="wizard-step-number">4</div>
                <span class="wizard-step-label">Checkout</span>
            </div>
        </div>

        <!-- Conteúdo do Passo -->
        <div class="wizard-content" style="text-align: center;">
            <!-- Animação de Loading -->
            <div class="loading-animation" style="margin: 3rem 0;">
                <div class="magic-wand">
                    <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 4V2"></path>
                        <path d="M15 16v-2"></path>
                        <path d="M8 9h2"></path>
                        <path d="M20 9h2"></path>
                        <path d="M17.8 11.8 19 13"></path>
                        <path d="M15 9h0"></path>
                        <path d="M17.8 6.2 19 5"></path>
                        <path d="m3 21 9-9"></path>
                        <path d="M12.2 6.2 11 5"></path>
                    </svg>
                    <div class="sparkles"></div>
                </div>
            </div>

            <!-- Título e Mensagem -->
            <h1 class="gradient-text" style="margin-bottom: 1rem;">
                ✨ A Magia Está Acontecendo...
            </h1>
            <p class="text-muted" style="font-size: 1.125rem; margin-bottom: 3rem;" id="status-message">
                Nossa IA está criando uma história única e personalizada
            </p>

            <!-- Barra de Progresso -->
            <div class="progress-bar-container">
                <div class="progress-bar" id="progress-bar" style="width: 0%"></div>
            </div>
            <p class="text-muted" style="font-size: 0.875rem; margin-top: 0.5rem;">
                <span id="progress-percentage">0</span>% concluído
            </p>

            <!-- Etapas do Processo -->
            <div style="margin-top: 3rem; text-align: left; max-width: 500px; margin-left: auto; margin-right: auto;">
                <div class="process-step" id="step-analyzing" data-step="analyzing">
                    <div class="step-icon">⏳</div>
                    <div>
                        <h4>Analisando a Foto</h4>
                        <p class="text-muted">Identificando características...</p>
                    </div>
                </div>

                <div class="process-step" id="step-creating" data-step="creating">
                    <div class="step-icon">📝</div>
                    <div>
                        <h4>Criando a História</h4>
                        <p class="text-muted">Gerando narrativa personalizada...</p>
                    </div>
                </div>

                <div class="process-step" id="step-illustrating" data-step="illustrating">
                    <div class="step-icon">🎨</div>
                    <div>
                        <h4>Criando Ilustrações</h4>
                        <p class="text-muted">Desenhando cenas mágicas...</p>
                    </div>
                </div>

                <div class="process-step" id="step-finalizing" data-step="finalizing">
                    <div class="step-icon">📚</div>
                    <div>
                        <h4>Finalizando o Livro</h4>
                        <p class="text-muted">Montando PDF...</p>
                    </div>
                </div>
            </div>

            <!-- Tempo Estimado -->
            <div class="card" style="margin-top: 3rem; padding: 1.5rem; background: rgba(139, 92, 246, 0.1); border: 2px solid var(--color-primary);">
                <p style="margin: 0; font-size: 0.875rem; color: var(--color-muted-foreground);">
                    ⏱️ Tempo estimado: <strong style="color: var(--color-primary);">3-5 minutos</strong>
                </p>
                <p style="margin: 0.5rem 0 0; font-size: 0.875rem; color: var(--color-muted-foreground);">
                    Você pode fechar esta página - enviaremos um email quando estiver pronto!
                </p>
            </div>

            <!-- Ordem ID (hidden para debugging) -->
            <input type="hidden" id="order-id" value="<?php echo htmlspecialchars($orderId ?? ''); ?>">
        </div>
    </div>
</div>

<script>
let orderId = document.getElementById('order-id').value;
let progressPercentage = 0;
let currentStep = 'analyzing';
let pollingInterval;

// Inicia processo
async function startProcessing() {
    if (!orderId) {
        // Se não tem order_id, cria o pedido primeiro
        await createOrder();
    }

    // Inicia polling
    startPolling();

    // Simula progresso visual
    simulateProgress();
}

// Cria pedido
async function createOrder() {
    try {
        const theme = sessionStorage.getItem('wizard_theme');
        const photoPath = sessionStorage.getItem('wizard_photo_path');
        const childName = sessionStorage.getItem('wizard_child_name');
        const childAge = sessionStorage.getItem('wizard_child_age');
        const childGender = sessionStorage.getItem('wizard_child_gender');
        const characteristics = sessionStorage.getItem('wizard_characteristics');
        const dedication = sessionStorage.getItem('wizard_dedication');

        const response = await fetch('<?php echo url('api/create-order.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                theme,
                photo_path: photoPath,
                child_name: childName,
                child_age: childAge,
                child_gender: childGender,
                characteristics,
                dedication
            })
        });

        const result = await response.json();

        if (result.success) {
            orderId = result.order_id;
            document.getElementById('order-id').value = orderId;
        } else {
            throw new Error(result.message || 'Erro ao criar pedido');
        }
    } catch (error) {
        console.error('Erro ao criar pedido:', error);
        alert('Erro ao iniciar processamento. Por favor, tente novamente.');
        window.location.href = '<?php echo url('pages/create/step2-photo.php'); ?>';
    }
}

// Polling de status
function startPolling() {
    pollingInterval = setInterval(async () => {
        await checkStatus();
    }, <?php echo STATUS_POLLING_INTERVAL; ?>); // 3 segundos
}

// Verifica status
async function checkStatus() {
    if (!orderId) return;

    try {
        const response = await fetch(`<?php echo url('api/check-order-status.php'); ?>?order_id=${orderId}`);
        const result = await response.json();

        if (result.success) {
            updateProgress(result.status, result.progress || 0);

            // Se completado, redireciona
            if (result.status === 'completed') {
                clearInterval(pollingInterval);
                setTimeout(() => {
                    window.location.href = `<?php echo url('pages/create/step4-checkout.php'); ?>?order_id=${orderId}`;
                }, 1500);
            }

            // Se falhou, mostra erro
            if (result.status === 'failed') {
                clearInterval(pollingInterval);
                alert('Ops! Algo deu errado. Nossa equipe foi notificada.');
                window.location.href = '<?php echo url('pages/dashboard.php'); ?>';
            }
        }
    } catch (error) {
        console.error('Erro ao verificar status:', error);
    }
}

// Atualiza progresso visual
function updateProgress(status, progress) {
    // Atualiza barra
    document.getElementById('progress-bar').style.width = progress + '%';
    document.getElementById('progress-percentage').textContent = progress;

    // Atualiza steps
    const steps = ['analyzing', 'creating', 'illustrating', 'finalizing'];
    const statusToStep = {
        'pending': 'analyzing',
        'paid': 'analyzing',
        'generating': 'creating',
        'processing': 'illustrating',
        'completed': 'finalizing'
    };

    const activeStep = statusToStep[status] || 'analyzing';

    steps.forEach((step, index) => {
        const stepEl = document.getElementById(`step-${step}`);
        const currentIndex = steps.indexOf(activeStep);

        if (index < currentIndex) {
            stepEl.classList.add('completed');
            stepEl.classList.remove('active');
        } else if (index === currentIndex) {
            stepEl.classList.add('active');
            stepEl.classList.remove('completed');
        } else {
            stepEl.classList.remove('active', 'completed');
        }
    });
}

// Simula progresso suave
function simulateProgress() {
    let simulatedProgress = 0;

    const progressSimulation = setInterval(() => {
        if (simulatedProgress < 95) {
            simulatedProgress += Math.random() * 5;
            updateProgress(currentStep, Math.min(Math.floor(simulatedProgress), 95));
        }
    }, 2000);

    // Para simulação após 5 minutos
    setTimeout(() => {
        clearInterval(progressSimulation);
    }, 5 * 60 * 1000);
}

// Inicia quando página carregar
window.addEventListener('DOMContentLoaded', () => {
    startProcessing();
});

// Limpa interval ao sair da página
window.addEventListener('beforeunload', () => {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
});
</script>

<style>
.magic-wand {
    position: relative;
    display: inline-block;
    animation: float 3s ease-in-out infinite;
}

.magic-wand svg {
    color: var(--color-primary);
    filter: drop-shadow(0 0 20px rgba(139, 92, 246, 0.5));
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(10deg); }
}

.sparkles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.progress-bar-container {
    width: 100%;
    height: 12px;
    background: var(--color-muted);
    border-radius: 999px;
    overflow: hidden;
    position: relative;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--color-primary), var(--color-secondary));
    border-radius: 999px;
    transition: width 0.5s ease;
    position: relative;
}

.progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.process-step {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    border-radius: var(--radius);
    margin-bottom: 0.75rem;
    opacity: 0.5;
    transition: all var(--transition-base);
}

.process-step.active {
    background: rgba(139, 92, 246, 0.1);
    border: 2px solid var(--color-primary);
    opacity: 1;
}

.process-step.active .step-icon {
    animation: pulse 1.5s ease-in-out infinite;
}

.process-step.completed {
    opacity: 1;
}

.process-step.completed .step-icon::after {
    content: ' ✓';
    color: var(--color-success);
}

.step-icon {
    font-size: 1.5rem;
    flex-shrink: 0;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.process-step h4 {
    margin: 0 0 0.25rem;
    font-size: 1rem;
    font-weight: 600;
}

.process-step p {
    margin: 0;
    font-size: 0.875rem;
}
</style>

<?php require_once __DIR__ . '/../../components/footer.php'; ?>
