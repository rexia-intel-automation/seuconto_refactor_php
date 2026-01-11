<?php
$pageTitle = 'Criar Meu Livro - Seu Conto';
$additionalCSS = [];
$additionalJS = [];

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
.step-container { max-width: 800px; margin: 3rem auto; padding: 2rem; }
.step-indicator { display: flex; justify-content: space-between; margin-bottom: 3rem; }
.step { flex: 1; text-align: center; position: relative; }
.step::after { content: ''; position: absolute; top: 20px; left: 50%; width: 100%; height: 2px; background: var(--color-border); z-index: 0; }
.step:last-child::after { display: none; }
.step-number { width: 40px; height: 40px; border-radius: 50%; background: var(--color-muted); color: var(--color-muted-foreground); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; font-weight: 700; position: relative; z-index: 1; }
.step.active .step-number { background: var(--color-primary); color: white; }
.step.completed .step-number { background: var(--color-success); color: white; }
.step-content { display: none; }
.step-content.active { display: block; }
.theme-card { cursor: pointer; transition: all var(--transition-base); }
.theme-card:hover, .theme-card.selected { transform: translateY(-8px); box-shadow: var(--shadow-lg); border-color: var(--color-primary); }
</style>

<div class="step-container container">
    <div class="step-indicator">
        <div class="step active" id="indicator-1">
            <div class="step-number">1</div>
            <p style="font-size: 0.875rem;">Dados da Criança</p>
        </div>
        <div class="step" id="indicator-2">
            <div class="step-number">2</div>
            <p style="font-size: 0.875rem;">Tema</p>
        </div>
        <div class="step" id="indicator-3">
            <div class="step-number">3</div>
            <p style="font-size: 0.875rem;">Seus Dados</p>
        </div>
    </div>

    <form id="create-form">
        <!-- Passo 1: Dados da Criança -->
        <div class="step-content active" id="step-1">
            <div class="card" style="padding: 2rem;">
                <h2 style="margin-bottom: 2rem;">Conte sobre a criança</h2>
                <div class="form-group">
                    <label class="form-label">Nome da Criança</label>
                    <input type="text" name="childName" class="form-input" placeholder="Ana" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Idade</label>
                    <select name="childAge" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php for($i=0; $i<=12; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?> anos</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Gênero</label>
                    <div style="display: flex; gap: 1rem;">
                        <label style="flex: 1; cursor: pointer;"><input type="radio" name="childGender" value="menino" required style="margin-right: 0.5rem;">Menino</label>
                        <label style="flex: 1; cursor: pointer;"><input type="radio" name="childGender" value="menina" required style="margin-right: 0.5rem;">Menina</label>
                        <label style="flex: 1; cursor: pointer;"><input type="radio" name="childGender" value="outro" required style="margin-right: 0.5rem;">Outro</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Características (opcional)</label>
                    <textarea name="childCharacteristics" class="form-textarea" placeholder="Ex: Cabelo loiro, olhos azuis, ama dinossauros..."></textarea>
                </div>
                <button type="button" class="btn btn-primary btn-full" onclick="nextStep(2)">Próximo →</button>
            </div>
        </div>

        <!-- Passo 2: Tema -->
        <div class="step-content" id="step-2">
            <div class="card" style="padding: 2rem;">
                <h2 style="margin-bottom: 2rem;">Escolha o tema da história</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <div class="card theme-card" onclick="selectTheme('coragem')" style="border: 2px solid oklch(0.65 0.20 240);">
                        <div style="font-size: 3rem; margin-bottom: 0.5rem;">🐉</div>
                        <h3 style="color: oklch(0.50 0.25 240);">Coragem</h3>
                    </div>
                    <div class="card theme-card" onclick="selectTheme('amizade')" style="border: 2px solid oklch(0.75 0.18 340);">
                        <div style="font-size: 3rem; margin-bottom: 0.5rem;">🤝</div>
                        <h3 style="color: oklch(0.60 0.20 340);">Amizade</h3>
                    </div>
                    <div class="card theme-card" onclick="selectTheme('exploracao')" style="border: 2px solid oklch(0.70 0.18 145);">
                        <div style="font-size: 3rem; margin-bottom: 0.5rem;">🦖</div>
                        <h3 style="color: oklch(0.50 0.20 145);">Exploração</h3>
                    </div>
                    <div class="card theme-card" onclick="selectTheme('magia')" style="border: 2px solid var(--color-primary);">
                        <div style="font-size: 3rem; margin-bottom: 0.5rem;">🧚</div>
                        <h3 style="color: var(--color-primary);">Magia</h3>
                    </div>
                </div>
                <input type="hidden" name="theme" id="theme-input" required>
                <div class="form-group">
                    <label class="form-label">Dedicatória (opcional)</label>
                    <textarea name="dedication" class="form-textarea" placeholder="Ex: Para minha querida filha, com todo meu amor..."></textarea>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <button type="button" class="btn btn-outline btn-full" onclick="previousStep(1)">← Voltar</button>
                    <button type="button" class="btn btn-primary btn-full" onclick="nextStep(3)" id="next-step-2" disabled>Próximo →</button>
                </div>
            </div>
        </div>

        <!-- Passo 3: Seus Dados -->
        <div class="step-content" id="step-3">
            <div class="card" style="padding: 2rem;">
                <h2 style="margin-bottom: 2rem;">Seus dados para entrega</h2>
                <div class="form-group">
                    <label class="form-label">Seu Nome</label>
                    <input type="text" name="customerName" class="form-input" placeholder="João da Silva" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="customerEmail" class="form-input" placeholder="seu@email.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label">WhatsApp (opcional)</label>
                    <input type="tel" name="customerPhone" class="form-input" placeholder="(11) 99999-9999">
                </div>
                <div class="form-group">
                    <label class="form-label">Método de Entrega</label>
                    <div style="display: flex; gap: 1rem;">
                        <label style="flex: 1; cursor: pointer;"><input type="radio" name="deliveryMethod" value="email" required checked style="margin-right: 0.5rem;">Email</label>
                        <label style="flex: 1; cursor: pointer;"><input type="radio" name="deliveryMethod" value="whatsapp" required style="margin-right: 0.5rem;">WhatsApp</label>
                        <label style="flex: 1; cursor: pointer;"><input type="radio" name="deliveryMethod" value="both" required style="margin-right: 0.5rem;">Ambos</label>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <button type="button" class="btn btn-outline btn-full" onclick="previousStep(2)">← Voltar</button>
                    <button type="submit" class="btn btn-primary btn-full">Ir para Checkout →</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let currentStep = 1;
let selectedTheme = null;

function nextStep(step) {
    if (step === 2) {
        const name = document.querySelector('[name="childName"]').value;
        const age = document.querySelector('[name="childAge"]').value;
        const gender = document.querySelector('[name="childGender"]:checked');
        if (!name || !age || !gender) {
            window.SeuConto.showToast('Preencha todos os campos obrigatórios', 'warning');
            return;
        }
    }
    if (step === 3 && !selectedTheme) {
        window.SeuConto.showToast('Escolha um tema', 'warning');
        return;
    }

    document.getElementById(`step-${currentStep}`).classList.remove('active');
    document.getElementById(`indicator-${currentStep}`).classList.remove('active');
    document.getElementById(`indicator-${currentStep}`).classList.add('completed');
    
    currentStep = step;
    document.getElementById(`step-${step}`).classList.add('active');
    document.getElementById(`indicator-${step}`).classList.add('active');
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function previousStep(step) {
    document.getElementById(`step-${currentStep}`).classList.remove('active');
    document.getElementById(`indicator-${currentStep}`).classList.remove('active');
    
    currentStep = step;
    document.getElementById(`step-${step}`).classList.add('active');
    document.getElementById(`indicator-${step}`).classList.remove('completed');
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function selectTheme(theme) {
    selectedTheme = theme;
    document.getElementById('theme-input').value = theme;
    document.getElementById('next-step-2').disabled = false;
    document.querySelectorAll('.theme-card').forEach(card => card.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
}

document.getElementById('create-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    window.SeuConto.saveToStorage('storyFormData', data);
    window.location.href = '/refactor/pages/checkout.php';
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
