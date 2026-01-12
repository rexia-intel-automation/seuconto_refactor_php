/**
 * Creation Flow - Gerenciador do Wizard de Criação
 *
 * Gerencia o fluxo de 4 etapas para criar um livro personalizado:
 * 1. Escolha do tema
 * 2. Upload da foto e dados da criança
 * 3. Processamento (polling de status)
 * 4. Checkout e pagamento
 */

class CreationFlow {
    constructor() {
        this.currentStep = 1;
        this.maxSteps = 4;
        // BASE_PATH é definido no head.php via <script>window.BASE_PATH = '...';</script>
        this.basePath = window.BASE_PATH || '';
        this.data = this.loadFromStorage() || {
            theme: null,
            childName: null,
            childAge: null,
            photoFile: null,
            photoUrl: null,
            orderId: null
        };

        this.init();
    }

    /**
     * Gera URL completa com base path
     * @param {string} path - Caminho relativo (ex: '/api/upload.php')
     * @returns {string} URL completa
     */
    url(path) {
        return this.basePath + path;
    }

    /**
     * Inicializa o fluxo baseado na página atual
     */
    init() {
        // Detectar step atual pela URL
        const path = window.location.pathname;

        if (path.includes('step1-theme')) {
            this.currentStep = 1;
            this.initStep1();
        } else if (path.includes('step2-photo')) {
            this.currentStep = 2;
            this.initStep2();
        } else if (path.includes('step3-processing')) {
            this.currentStep = 3;
            this.initStep3();
        } else if (path.includes('step4-checkout')) {
            this.currentStep = 4;
            this.initStep4();
        }

        // Atualizar indicador de progresso
        this.updateProgressBar();
    }

    /**
     * Step 1: Seleção de Tema
     */
    initStep1() {
        console.log('📚 Inicializando Step 1: Seleção de Tema');

        const themeCards = document.querySelectorAll('.theme-card');
        const nextButton = document.getElementById('nextButton');

        themeCards.forEach(card => {
            card.addEventListener('click', () => {
                // Remover seleção anterior
                themeCards.forEach(c => c.classList.remove('selected'));

                // Selecionar tema
                card.classList.add('selected');
                this.data.theme = card.dataset.theme;

                // Habilitar botão next
                if (nextButton) {
                    nextButton.disabled = false;
                }

                // Salvar no storage
                this.saveToStorage();
            });
        });

        // Restaurar seleção anterior se existir
        if (this.data.theme) {
            const selectedCard = document.querySelector(`[data-theme="${this.data.theme}"]`);
            if (selectedCard) {
                selectedCard.classList.add('selected');
                if (nextButton) nextButton.disabled = false;
            }
        }

        // Handler do botão next
        if (nextButton) {
            nextButton.addEventListener('click', () => {
                if (this.data.theme) {
                    this.goToStep(2);
                }
            });
        }
    }

    /**
     * Step 2: Upload de Foto e Dados da Criança
     */
    initStep2() {
        console.log('📸 Inicializando Step 2: Upload de Foto');

        const uploadArea = document.getElementById('uploadArea');
        const photoInput = document.getElementById('photoInput');
        const childNameInput = document.getElementById('childName');
        const childAgeInput = document.getElementById('childAge');
        const nextButton = document.getElementById('nextButton');
        const previewContainer = document.getElementById('photoPreview');

        // Drag & Drop
        if (uploadArea) {
            uploadArea.addEventListener('click', () => photoInput?.click());

            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    this.handleFileUpload(files[0]);
                }
            });
        }

        // File input change
        if (photoInput) {
            photoInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    this.handleFileUpload(e.target.files[0]);
                }
            });
        }

        // Validação em tempo real
        [childNameInput, childAgeInput].forEach(input => {
            if (input) {
                input.addEventListener('input', () => {
                    this.validateStep2Form();
                });
            }
        });

        // Restaurar dados anteriores
        if (this.data.childName && childNameInput) {
            childNameInput.value = this.data.childName;
        }
        if (this.data.childAge && childAgeInput) {
            childAgeInput.value = this.data.childAge;
        }
        if (this.data.photoUrl && previewContainer) {
            this.showPhotoPreview(this.data.photoUrl);
        }

        // Handler do botão next
        if (nextButton) {
            nextButton.addEventListener('click', async () => {
                if (this.validateStep2Form()) {
                    await this.createOrder();
                }
            });
        }

        this.validateStep2Form();
    }

    /**
     * Upload de arquivo
     */
    async handleFileUpload(file) {
        // Validar tipo
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            this.showError('Tipo de arquivo não permitido. Use JPEG, PNG ou WebP.');
            return;
        }

        // Validar tamanho (5MB)
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            this.showError('Arquivo muito grande. Tamanho máximo: 5MB.');
            return;
        }

        // Mostrar loading
        this.showUploadProgress(true);

        try {
            // Fazer upload
            const formData = new FormData();
            formData.append('photo', file);

            const response = await fetch(this.url('/api/upload-photo.php'), {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.data.photoUrl = result.data.url;
                this.data.photoFile = result.data.filename;
                this.saveToStorage();

                this.showPhotoPreview(result.data.url);
                this.showSuccess('Foto enviada com sucesso!');
                this.validateStep2Form();
            } else {
                throw new Error(result.error || 'Erro ao fazer upload');
            }
        } catch (error) {
            console.error('Erro no upload:', error);
            this.showError(error.message || 'Erro ao enviar foto. Tente novamente.');
        } finally {
            this.showUploadProgress(false);
        }
    }

    /**
     * Mostrar preview da foto
     */
    showPhotoPreview(url) {
        const previewContainer = document.getElementById('photoPreview');
        const uploadArea = document.getElementById('uploadArea');

        if (previewContainer) {
            previewContainer.innerHTML = `
                <div class="photo-preview-wrapper">
                    <img src="${url}" alt="Preview" class="photo-preview-image">
                    <button type="button" class="btn btn-sm btn-danger" onclick="creationFlow.removePhoto()">
                        Remover Foto
                    </button>
                </div>
            `;
            previewContainer.style.display = 'block';
        }

        if (uploadArea) {
            uploadArea.style.display = 'none';
        }
    }

    /**
     * Remover foto
     */
    removePhoto() {
        this.data.photoUrl = null;
        this.data.photoFile = null;
        this.saveToStorage();

        const previewContainer = document.getElementById('photoPreview');
        const uploadArea = document.getElementById('uploadArea');

        if (previewContainer) previewContainer.style.display = 'none';
        if (uploadArea) uploadArea.style.display = 'block';

        this.validateStep2Form();
    }

    /**
     * Validar formulário do Step 2
     */
    validateStep2Form() {
        const childNameInput = document.getElementById('childName');
        const childAgeInput = document.getElementById('childAge');
        const nextButton = document.getElementById('nextButton');

        const childName = childNameInput?.value.trim();
        const childAge = childAgeInput?.value;
        const hasPhoto = !!this.data.photoUrl;

        const isValid = childName && childAge && hasPhoto;

        if (nextButton) {
            nextButton.disabled = !isValid;
        }

        // Salvar dados
        if (childName) this.data.childName = childName;
        if (childAge) this.data.childAge = childAge;
        this.saveToStorage();

        return isValid;
    }

    /**
     * Step 3: Processamento
     */
    initStep3() {
        console.log('⏳ Inicializando Step 3: Processamento');

        // Iniciar polling de status
        this.startStatusPolling();
    }

    /**
     * Polling de status do pedido
     */
    async startStatusPolling() {
        const orderId = this.data.orderId || new URLSearchParams(window.location.search).get('order_id');

        if (!orderId) {
            console.error('Nenhum order_id encontrado');
            this.showError('Pedido não encontrado');
            return;
        }

        this.data.orderId = orderId;
        this.saveToStorage();

        const poll = async () => {
            try {
                const response = await fetch(this.url(`/api/check-order-status.php?order_id=${orderId}`));
                const result = await response.json();

                if (result.success) {
                    this.updateProcessingStatus(result.data);

                    // Se concluído, redirecionar para checkout
                    if (result.data.status === 'completed') {
                        setTimeout(() => {
                            this.goToStep(4);
                        }, 2000);
                        return;
                    }

                    // Se falhou, mostrar erro
                    if (result.data.status === 'failed') {
                        this.showError('Falha na geração do livro. Tente novamente.');
                        return;
                    }

                    // Continuar polling
                    setTimeout(poll, 3000); // A cada 3 segundos
                } else {
                    throw new Error(result.error);
                }
            } catch (error) {
                console.error('Erro no polling:', error);
                setTimeout(poll, 5000); // Tentar novamente em 5s
            }
        };

        poll();
    }

    /**
     * Atualizar UI de processamento
     */
    updateProcessingStatus(data) {
        const statusText = document.getElementById('statusText');
        const progressBar = document.getElementById('progressBar');

        if (statusText) {
            statusText.textContent = data.status_message || 'Processando...';
        }

        if (progressBar) {
            const progress = data.progress || 50;
            progressBar.style.width = `${progress}%`;
        }
    }

    /**
     * Step 4: Checkout
     */
    initStep4() {
        console.log('💳 Inicializando Step 4: Checkout');

        const checkoutButton = document.getElementById('checkoutButton');

        if (checkoutButton) {
            checkoutButton.addEventListener('click', async () => {
                await this.processCheckout();
            });
        }
    }

    /**
     * Criar pedido
     */
    async createOrder() {
        try {
            this.showLoading(true);

            const response = await fetch(this.url('/api/create-order.php'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    theme: this.data.theme,
                    child_name: this.data.childName,
                    child_age: this.data.childAge,
                    photo_file: this.data.photoFile
                })
            });

            const result = await response.json();

            if (result.success) {
                this.data.orderId = result.data.order_id;
                this.saveToStorage();
                this.goToStep(3);
            } else {
                throw new Error(result.error || 'Erro ao criar pedido');
            }
        } catch (error) {
            console.error('Erro ao criar pedido:', error);
            this.showError(error.message || 'Erro ao criar pedido. Tente novamente.');
        } finally {
            this.showLoading(false);
        }
    }

    /**
     * Processar checkout
     */
    async processCheckout() {
        // Implementar integração com Stripe
        console.log('Processando checkout para pedido:', this.data.orderId);
        this.showError('Checkout ainda não implementado');
    }

    /**
     * Navegar para step
     */
    goToStep(step) {
        const stepUrls = {
            1: this.url('/pages/create/step1-theme.php'),
            2: this.url('/pages/create/step2-photo.php'),
            3: this.url(`/pages/create/step3-processing.php?order_id=${this.data.orderId}`),
            4: this.url(`/pages/create/step4-checkout.php?order_id=${this.data.orderId}`)
        };

        if (stepUrls[step]) {
            window.location.href = stepUrls[step];
        }
    }

    /**
     * Atualizar barra de progresso
     */
    updateProgressBar() {
        const progressSteps = document.querySelectorAll('.progress-step');
        const progressBar = document.querySelector('.progress-bar-fill');

        if (progressSteps) {
            progressSteps.forEach((step, index) => {
                if (index + 1 < this.currentStep) {
                    step.classList.add('completed');
                    step.classList.remove('active');
                } else if (index + 1 === this.currentStep) {
                    step.classList.add('active');
                    step.classList.remove('completed');
                } else {
                    step.classList.remove('active', 'completed');
                }
            });
        }

        if (progressBar) {
            const progress = ((this.currentStep - 1) / (this.maxSteps - 1)) * 100;
            progressBar.style.width = `${progress}%`;
        }
    }

    /**
     * Helpers de UI
     */
    showLoading(show) {
        const loader = document.getElementById('loadingOverlay');
        if (loader) {
            loader.style.display = show ? 'flex' : 'none';
        }
    }

    showUploadProgress(show) {
        const progress = document.getElementById('uploadProgress');
        if (progress) {
            progress.style.display = show ? 'block' : 'none';
        }
    }

    showError(message) {
        // Implementar toast/alert de erro
        alert('Erro: ' + message);
    }

    showSuccess(message) {
        // Implementar toast/alert de sucesso
        console.log('Sucesso:', message);
    }

    /**
     * LocalStorage helpers
     */
    saveToStorage() {
        localStorage.setItem('creation_flow_data', JSON.stringify(this.data));
    }

    loadFromStorage() {
        const data = localStorage.getItem('creation_flow_data');
        return data ? JSON.parse(data) : null;
    }

    clearStorage() {
        localStorage.removeItem('creation_flow_data');
    }
}

// Inicializar quando a página carregar
let creationFlow;
document.addEventListener('DOMContentLoaded', () => {
    creationFlow = new CreationFlow();
});

// Exportar para uso global
window.creationFlow = creationFlow;
