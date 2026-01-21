<?php
/**
 * Wizard de Criação - Passo 2: Foto e Dados da Criança
 */

// Carrega dependências
require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

$pageTitle = 'Foto e Dados - Criar Livro | Seu Conto';
$pageDescription = 'Envie a foto e conte sobre a criança';
$additionalCSS = [asset('css/wizard.css')];

// Requer autenticação
requireAuth();
$currentUser = getCurrentUser();
$isLoggedIn = isLoggedIn();

// Verifica se tema foi selecionado
session_start();
if (!isset($_SESSION['wizard_theme']) && empty($_GET['theme'])) {
    header('Location: ' . url('pages/create/step1-theme.php'));
    exit;
}

if (!empty($_GET['theme'])) {
    $_SESSION['wizard_theme'] = $_GET['theme'];
}

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
            <div class="wizard-step active">
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
                <h1 class="gradient-text">Foto e Informações da Criança</h1>
                <p class="text-muted" style="font-size: 1.125rem; margin-top: 1rem;">
                    Uma foto ajuda nossa IA a criar ilustrações mais personalizadas
                </p>
            </div>

            <form id="photo-form" method="POST" enctype="multipart/form-data" style="margin-top: 3rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
                    <!-- Coluna Esquerda: Upload de Foto -->
                    <div>
                        <h3 style="margin-bottom: 1.5rem;">📸 Foto da Criança</h3>

                        <!-- Upload Area -->
                        <div class="upload-area" id="upload-area">
                            <div id="upload-placeholder">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom: 1rem; opacity: 0.3;">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                                <p style="font-weight: 600; margin-bottom: 0.5rem;">Clique ou arraste a foto aqui</p>
                                <p class="text-muted" style="font-size: 0.875rem;">JPG, PNG ou WEBP (máx. 5MB)</p>
                            </div>
                            <img id="photo-preview" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-lg);">
                            <input type="file" id="photo-input" name="photo" accept="image/jpeg,image/png,image/webp" style="display: none;" required>
                            <button type="button" id="change-photo-btn" class="btn btn-sm btn-outline" style="display: none; position: absolute; bottom: 1rem; right: 1rem;">
                                Trocar Foto
                            </button>
                        </div>

                        <p class="text-muted" style="font-size: 0.875rem; margin-top: 1rem;">
                            💡 <strong>Dica:</strong> Use uma foto nítida do rosto da criança, com boa iluminação
                        </p>
                    </div>

                    <!-- Coluna Direita: Dados -->
                    <div>
                        <h3 style="margin-bottom: 1.5rem;">✏️ Informações</h3>

                        <div class="form-group">
                            <label class="form-label">Nome da Criança *</label>
                            <input type="text" name="child_name" class="form-input" placeholder="Ex: Maria" required maxlength="<?php echo MAX_CHILD_NAME_LENGTH; ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Idade *</label>
                            <select name="child_age" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php for($i = MIN_CHILD_AGE; $i <= MAX_CHILD_AGE; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> anos</option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Gênero *</label>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;">
                                <?php foreach(AVAILABLE_GENDERS as $gender): ?>
                                    <label class="radio-card">
                                        <input type="radio" name="child_gender" value="<?php echo $gender; ?>" required>
                                        <span><?php echo ucfirst($gender); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Características (opcional)</label>
                            <textarea name="characteristics" class="form-textarea" placeholder="Ex: Cabelo castanho, olhos verdes, ama dinossauros..." rows="3" maxlength="<?php echo MAX_CHARACTERISTICS_LENGTH; ?>"></textarea>
                            <small class="text-muted">Ajuda a IA a criar personagens mais parecidos</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Dedicatória (opcional)</label>
                            <textarea name="dedication" class="form-textarea" placeholder="Ex: Para minha querida filha, com todo meu amor..." rows="2" maxlength="<?php echo MAX_DEDICATION_LENGTH; ?>"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Botões de Navegação -->
                <div class="wizard-actions" style="margin-top: 3rem;">
                    <a href="<?php echo url('pages/create/step1-theme.php'); ?>" class="btn btn-outline">
                        ← Voltar ao Tema
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg" id="submit-button">
                        Próximo: Processar →
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const uploadArea = document.getElementById('upload-area');
const photoInput = document.getElementById('photo-input');
const photoPreview = document.getElementById('photo-preview');
const uploadPlaceholder = document.getElementById('upload-placeholder');
const changePhotoBtn = document.getElementById('change-photo-btn');
const submitButton = document.getElementById('submit-button');

// Click na área abre seletor de arquivo
uploadArea.addEventListener('click', function(e) {
    if (e.target !== changePhotoBtn && !changePhotoBtn.contains(e.target)) {
        photoInput.click();
    }
});

// Drag & Drop
uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    uploadArea.style.borderColor = 'var(--color-primary)';
    uploadArea.style.background = 'rgba(139, 92, 246, 0.05)';
});

uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    uploadArea.style.borderColor = 'var(--color-border)';
    uploadArea.style.background = 'var(--color-muted)';
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    uploadArea.style.borderColor = 'var(--color-border)';
    uploadArea.style.background = 'var(--color-muted)';

    const files = e.dataTransfer.files;
    if (files.length > 0) {
        handleFile(files[0]);
    }
});

// Seleção de arquivo
photoInput.addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
        handleFile(e.target.files[0]);
    }
});

// Trocar foto
changePhotoBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    photoInput.click();
});

function handleFile(file) {
    // Valida tipo
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        alert('Tipo de arquivo não permitido. Use JPG, PNG ou WEBP.');
        return;
    }

    // Valida tamanho (5MB)
    const maxSize = 5 * 1024 * 1024;
    if (file.size > maxSize) {
        alert('Arquivo muito grande. Tamanho máximo: 5MB');
        return;
    }

    // Preview
    const reader = new FileReader();
    reader.onload = function(e) {
        photoPreview.src = e.target.result;
        photoPreview.style.display = 'block';
        uploadPlaceholder.style.display = 'none';
        changePhotoBtn.style.display = 'block';
    };
    reader.readAsDataURL(file);
}

// Submit do formulário
document.getElementById('photo-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    // Valida se foto foi selecionada
    if (!photoInput.files || photoInput.files.length === 0) {
        alert('Por favor, selecione uma foto da criança');
        return;
    }

    // Desabilita botão durante upload
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner"></span> Enviando...';

    const formData = new FormData(this);

    try {
        // Upload da foto
        const response = await fetch('<?php echo url('api/upload-photo.php'); ?>', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            // Salva dados na sessão
            sessionStorage.setItem('wizard_photo_path', result.photo_path);
            sessionStorage.setItem('wizard_child_name', formData.get('child_name'));
            sessionStorage.setItem('wizard_child_age', formData.get('child_age'));
            sessionStorage.setItem('wizard_child_gender', formData.get('child_gender'));
            sessionStorage.setItem('wizard_characteristics', formData.get('characteristics'));
            sessionStorage.setItem('wizard_dedication', formData.get('dedication'));

            // Redireciona para step 3
            window.location.href = '<?php echo url('pages/create/step3-processing.php'); ?>';
        } else {
            throw new Error(result.message || 'Erro ao enviar foto');
        }
    } catch (error) {
        alert('Erro ao processar: ' + error.message);
        submitButton.disabled = false;
        submitButton.innerHTML = 'Próximo: Processar →';
    }
});

// Carrega dados salvos (se voltar)
window.addEventListener('DOMContentLoaded', function() {
    const savedName = sessionStorage.getItem('wizard_child_name');
    if (savedName) {
        document.querySelector('[name="child_name"]').value = savedName;
        document.querySelector('[name="child_age"]').value = sessionStorage.getItem('wizard_child_age') || '';

        const gender = sessionStorage.getItem('wizard_child_gender');
        if (gender) {
            document.querySelector(`[name="child_gender"][value="${gender}"]`).checked = true;
        }

        document.querySelector('[name="characteristics"]').value = sessionStorage.getItem('wizard_characteristics') || '';
        document.querySelector('[name="dedication"]').value = sessionStorage.getItem('wizard_dedication') || '';
    }
});
</script>

<style>
.upload-area {
    border: 3px dashed var(--color-border);
    border-radius: var(--radius-lg);
    background: var(--color-muted);
    padding: 3rem;
    text-align: center;
    cursor: pointer;
    transition: all var(--transition-base);
    position: relative;
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-area:hover {
    border-color: var(--color-primary);
    background: rgba(139, 92, 246, 0.05);
}

.radio-card {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem;
    border: 2px solid var(--color-border);
    border-radius: var(--radius);
    cursor: pointer;
    transition: all var(--transition-base);
    text-align: center;
}

.radio-card input {
    display: none;
}

.radio-card:hover {
    border-color: var(--color-primary);
    background: rgba(139, 92, 246, 0.05);
}

.radio-card input:checked + span {
    color: var(--color-primary);
    font-weight: 600;
}

.radio-card:has(input:checked) {
    border-color: var(--color-primary);
    background: rgba(139, 92, 246, 0.1);
    border-width: 3px;
}

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

@media (max-width: 768px) {
    .wizard-content > form > div {
        grid-template-columns: 1fr !important;
        gap: 2rem !important;
    }
}
</style>

<?php require_once __DIR__ . '/../../components/footer.php'; ?>
