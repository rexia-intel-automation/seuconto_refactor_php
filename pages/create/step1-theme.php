<?php
/**
 * Wizard de Criação - Passo 1: Escolha do Tema
 */

// Carrega dependências
require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

$pageTitle = 'Escolha o Tema - Criar Livro | Seu Conto';
$pageDescription = 'Escolha o tema perfeito para a história personalizada';
$additionalCSS = [asset('css/wizard.css')];

// Obtém dados do usuário
$currentUser = getCurrentUser();
$isLoggedIn = isLoggedIn();

// Inclui head + header
require_once __DIR__ . '/../../components/head.php';
require_once __DIR__ . '/../../components/header.php';
?>

<!-- Wizard Container -->
<div class="wizard-container">
    <div class="container" style="max-width: 1000px;">
        <!-- Indicador de Progresso -->
        <div class="wizard-progress">
            <div class="wizard-step active">
                <div class="wizard-step-number">1</div>
                <span class="wizard-step-label">Tema</span>
            </div>
            <div class="wizard-step-line"></div>
            <div class="wizard-step">
                <div class="wizard-step-number">2</div>
                <span class="wizard-step-label">Foto & Dados</span>
            </div>
            <div class="wizard-step-line"></div>
            <div class="wizard-step">
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
        <div class="wizard-content">
            <div class="wizard-header">
                <h1 class="gradient-text">Escolha o Tema da História Mágica</h1>
                <p class="text-muted" style="font-size: 1.125rem; margin-top: 1rem;">
                    Selecione o universo que você quer explorar com a criança
                </p>
            </div>

            <form id="theme-form" style="margin-top: 3rem;">
                <!-- Grid de Temas -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
                    <?php
                    $themes = AVAILABLE_THEMES;
                    foreach ($themes as $themeKey => $theme):
                    ?>
                        <div class="theme-card" data-theme="<?php echo $themeKey; ?>" onclick="selectTheme('<?php echo $themeKey; ?>')">
                            <div class="theme-card-icon" style="background: <?php echo $theme['color']; ?>;">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                    <?php if ($themeKey === 'aventura'): ?>
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="2" y1="12" x2="22" y2="12"></line>
                                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                    <?php elseif ($themeKey === 'fantasia'): ?>
                                        <path d="M15 4V2"></path>
                                        <path d="M15 16v-2"></path>
                                        <path d="M8 9h2"></path>
                                        <path d="M20 9h2"></path>
                                        <path d="M17.8 11.8 19 13"></path>
                                        <path d="M15 9h0"></path>
                                        <path d="M17.8 6.2 19 5"></path>
                                        <path d="m3 21 9-9"></path>
                                        <path d="M12.2 6.2 11 5"></path>
                                    <?php elseif ($themeKey === 'espaco'): ?>
                                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                        <path d="M2 17l10 5 10-5"></path>
                                        <path d="M2 12l10 5 10-5"></path>
                                    <?php elseif ($themeKey === 'animais'): ?>
                                        <path d="M11 4a2 2 0 0 1 2 0l7 4a2 2 0 0 1 1 1.7v8.6a2 2 0 0 1-1 1.7l-7 4a2 2 0 0 1-2 0l-7-4A2 2 0 0 1 3 18.3V9.7a2 2 0 0 1 1-1.7z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    <?php elseif ($themeKey === 'princesa'): ?>
                                        <path d="m2 17 4-4 4 4"></path>
                                        <path d="m10 13 4-4 4 4"></path>
                                        <path d="m18 9 4-4 4 4"></path>
                                        <path d="M2 17v5h20v-5"></path>
                                    <?php else: ?>
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    <?php endif; ?>
                                </svg>
                            </div>
                            <h3 class="theme-card-title"><?php echo e($theme['name']); ?></h3>
                            <p class="theme-card-description"><?php echo e($theme['description']); ?></p>
                            <div class="theme-card-badge">Selecionar</div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <input type="hidden" name="theme" id="selected-theme" required>

                <!-- Botões de Navegação -->
                <div class="wizard-actions">
                    <a href="<?php echo url('index.php'); ?>" class="btn btn-outline">
                        ← Voltar
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg" id="next-button" disabled>
                        Próximo: Enviar Foto →
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let selectedTheme = null;

function selectTheme(theme) {
    selectedTheme = theme;

    // Remove seleção anterior
    document.querySelectorAll('.theme-card').forEach(card => {
        card.classList.remove('selected');
    });

    // Adiciona seleção ao card clicado
    event.currentTarget.classList.add('selected');

    // Atualiza input hidden
    document.getElementById('selected-theme').value = theme;

    // Habilita botão próximo
    document.getElementById('next-button').disabled = false;
}

// Submit do formulário
document.getElementById('theme-form').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!selectedTheme) {
        alert('Por favor, selecione um tema');
        return;
    }

    // Salva tema na sessão/localStorage
    if (typeof(Storage) !== "undefined") {
        localStorage.setItem('wizard_theme', selectedTheme);
    }

    // Redireciona para próximo passo
    window.location.href = '<?php echo url('pages/create/step2-photo.php'); ?>';
});

// Carrega tema previamente selecionado (se voltar)
window.addEventListener('DOMContentLoaded', function() {
    if (typeof(Storage) !== "undefined") {
        const savedTheme = localStorage.getItem('wizard_theme');
        if (savedTheme) {
            const themeCard = document.querySelector(`[data-theme="${savedTheme}"]`);
            if (themeCard) {
                themeCard.click();
            }
        }
    }
});
</script>

<style>
.wizard-container {
    min-height: calc(100vh - 200px);
    padding: 4rem 0;
    background: linear-gradient(to bottom, var(--color-background) 0%, var(--color-muted) 100%);
}

.wizard-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 4rem;
    padding: 0 2rem;
}

.wizard-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}

.wizard-step-number {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--color-muted);
    color: var(--color-muted-foreground);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.25rem;
    transition: all var(--transition-base);
    border: 3px solid transparent;
}

.wizard-step.active .wizard-step-number {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.2);
}

.wizard-step.completed .wizard-step-number {
    background: var(--color-success);
    color: white;
}

.wizard-step-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-muted-foreground);
}

.wizard-step.active .wizard-step-label {
    color: var(--color-primary);
}

.wizard-step-line {
    flex: 1;
    height: 3px;
    background: var(--color-border);
    margin: 0 1rem;
    max-width: 100px;
}

.wizard-content {
    background: var(--color-card);
    border-radius: var(--radius-xl);
    padding: 3rem;
    box-shadow: var(--shadow-lg);
}

.wizard-header {
    text-align: center;
}

.theme-card {
    background: var(--color-card);
    border: 2px solid var(--color-border);
    border-radius: var(--radius-lg);
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all var(--transition-base);
    position: relative;
    overflow: hidden;
}

.theme-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
    border-color: var(--color-primary);
}

.theme-card.selected {
    border-color: var(--color-primary);
    border-width: 3px;
    box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.2);
}

.theme-card-icon {
    width: 80px;
    height: 80px;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}

.theme-card-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--color-foreground);
}

.theme-card-description {
    font-size: 0.875rem;
    color: var(--color-muted-foreground);
    margin-bottom: 1rem;
}

.theme-card-badge {
    display: inline-block;
    background: var(--color-muted);
    color: var(--color-muted-foreground);
    padding: 0.5rem 1rem;
    border-radius: var(--radius);
    font-size: 0.875rem;
    font-weight: 600;
    transition: all var(--transition-base);
}

.theme-card.selected .theme-card-badge {
    background: var(--color-primary);
    color: white;
}

.wizard-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    padding-top: 2rem;
    border-top: 1px solid var(--color-border);
}

@media (max-width: 768px) {
    .wizard-content {
        padding: 2rem 1.5rem;
    }

    .wizard-step-label {
        display: none;
    }

    .wizard-step-line {
        max-width: 40px;
        margin: 0 0.5rem;
    }

    .wizard-actions {
        flex-direction: column-reverse;
    }

    .wizard-actions .btn {
        width: 100%;
    }
}
</style>

<?php require_once __DIR__ . '/../../components/footer.php'; ?>
