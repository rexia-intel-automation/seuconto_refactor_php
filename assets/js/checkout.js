/**
 * SEU CONTO - JavaScript de Checkout
 *
 * Funcionalidades da página de checkout e processamento de pagamento
 */

// ============================================
// CONSTANTES
// ============================================
const PRICE_EBOOK = 2990;  // R$ 29,90 em centavos
const PRICE_COLORING_BOOK = 990;  // R$ 9,90 em centavos

// ============================================
// GERENCIAMENTO DE PREÇOS
// ============================================

/**
 * Calcula o total do pedido
 * @param {boolean} includeColoring - Se inclui livro de colorir
 * @returns {number} Total em centavos
 */
function calculateTotal(includeColoring = false) {
    let total = PRICE_EBOOK;

    if (includeColoring) {
        total += PRICE_COLORING_BOOK;
    }

    return total;
}

/**
 * Atualiza exibição de preços na UI
 */
function updatePriceDisplay() {
    const coloringCheckbox = document.getElementById('includeColoring');
    const includeColoring = coloringCheckbox ? coloringCheckbox.checked : false;

    const total = calculateTotal(includeColoring);

    // Atualiza elementos de preço
    const ebookPriceEl = document.getElementById('ebook-price');
    const coloringPriceEl = document.getElementById('coloring-price');
    const totalPriceEl = document.getElementById('total-price');

    if (ebookPriceEl) {
        ebookPriceEl.textContent = window.SeuConto.formatPrice(PRICE_EBOOK);
    }

    if (coloringPriceEl) {
        coloringPriceEl.textContent = window.SeuConto.formatPrice(PRICE_COLORING_BOOK);
    }

    if (totalPriceEl) {
        totalPriceEl.textContent = window.SeuConto.formatPrice(total);
    }

    // Atualiza visibilidade da linha de colorir
    const coloringRow = document.getElementById('coloring-row');
    if (coloringRow) {
        coloringRow.style.display = includeColoring ? 'flex' : 'none';
    }

    // Anima mudança de preço
    if (totalPriceEl) {
        totalPriceEl.classList.add('animate-pulse');
        setTimeout(() => {
            totalPriceEl.classList.remove('animate-pulse');
        }, 500);
    }
}

// ============================================
// RECUPERAR DADOS DO FORMULÁRIO
// ============================================

/**
 * Recupera dados salvos do formulário de criação
 * @returns {Object|null} Dados do formulário ou null
 */
function getStoredFormData() {
    return window.SeuConto.getFromStorage('storyFormData');
}

/**
 * Valida se há dados do formulário
 * @returns {boolean} True se há dados válidos
 */
function hasValidFormData() {
    const data = getStoredFormData();

    if (!data) return false;

    // Valida campos obrigatórios
    return !!(
        data.childName &&
        data.childAge &&
        data.childGender &&
        data.theme &&
        data.customerEmail
    );
}

/**
 * Exibe resumo do pedido
 */
function displayOrderSummary() {
    const data = getStoredFormData();

    if (!data) {
        window.SeuConto.showToast(
            'Nenhum livro em criação. Redirecionando...',
            'warning'
        );
        setTimeout(() => {
            window.location.href = window.BASE_PATH + '/pages/create/step1-theme.php';
        }, 2000);
        return;
    }

    // Atualiza elementos do resumo
    const elements = {
        childName: document.getElementById('summary-child-name'),
        childAge: document.getElementById('summary-child-age'),
        childGender: document.getElementById('summary-child-gender'),
        theme: document.getElementById('summary-theme'),
        characteristics: document.getElementById('summary-characteristics'),
        dedication: document.getElementById('summary-dedication'),
        customerName: document.getElementById('summary-customer-name'),
        customerEmail: document.getElementById('summary-customer-email')
    };

    if (elements.childName) elements.childName.textContent = data.childName;
    if (elements.childAge) elements.childAge.textContent = `${data.childAge} anos`;
    if (elements.childGender) elements.childGender.textContent = data.childGender;
    if (elements.theme) elements.theme.textContent = data.theme;
    if (elements.characteristics) {
        elements.characteristics.textContent = data.childCharacteristics || 'Não informado';
    }
    if (elements.dedication) {
        elements.dedication.textContent = data.dedication || 'Sem dedicatória';
    }
    if (elements.customerName) elements.customerName.textContent = data.customerName;
    if (elements.customerEmail) elements.customerEmail.textContent = data.customerEmail;
}

// ============================================
// PROCESSAMENTO DE CHECKOUT
// ============================================

/**
 * Processa o checkout com Stripe
 */
async function handleCheckout(event) {
    if (event) {
        event.preventDefault();
    }

    const submitButton = document.getElementById('checkout-button');

    // Validações
    if (!hasValidFormData()) {
        window.SeuConto.showToast(
            'Dados incompletos. Por favor, preencha o formulário novamente.',
            'error'
        );
        setTimeout(() => {
            window.location.href = window.BASE_PATH + '/pages/create/step1-theme.php';
        }, 2000);
        return;
    }

    const formData = getStoredFormData();
    const coloringCheckbox = document.getElementById('includeColoring');
    const includeColoring = coloringCheckbox ? coloringCheckbox.checked : false;

    // Monta payload
    const payload = {
        action: 'createCheckoutSession',
        customerName: formData.customerName,
        customerEmail: formData.customerEmail,
        customerPhone: formData.customerPhone || '',
        childName: formData.childName,
        childAge: parseInt(formData.childAge),
        childGender: formData.childGender,
        childCharacteristics: formData.childCharacteristics || '',
        theme: formData.theme,
        dedication: formData.dedication || '',
        includesColoringBook: includeColoring,
        deliveryMethod: formData.deliveryMethod || 'email',
        totalPrice: calculateTotal(includeColoring)
    };

    // Ativa loading
    window.SeuConto.setButtonLoading(submitButton);

    try {
        const response = await window.SeuConto.fetchAPI(window.BASE_PATH + '/api/checkout.php', {
            method: 'POST',
            body: JSON.stringify(payload)
        });

        if (response.success && response.checkoutUrl) {
            window.SeuConto.showToast(
                'Redirecionando para pagamento...',
                'success'
            );

            // Salva ID do pedido
            window.SeuConto.saveToStorage('currentOrderId', response.orderId);

            // Redireciona para Stripe
            setTimeout(() => {
                window.location.href = response.checkoutUrl;
            }, 500);

        } else {
            throw new Error(response.message || 'Erro ao criar sessão de pagamento');
        }

    } catch (error) {
        console.error('Erro no checkout:', error);
        window.SeuConto.showToast(error.message, 'error');
        window.SeuConto.removeButtonLoading(submitButton);
    }
}

/**
 * Solicita cópia física (futuro)
 */
function requestPhysicalCopy() {
    window.SeuConto.showToast(
        'Cópias físicas em breve! Deixe seu interesse.',
        'info',
        3000
    );

    // Aqui pode abrir um modal de interesse
    // Por enquanto apenas mostra mensagem
}

// ============================================
// CUPOM DE DESCONTO
// ============================================

/**
 * Aplica cupom de desconto (futuro)
 */
async function applyCoupon() {
    const couponInput = document.getElementById('coupon-code');
    const couponButton = document.getElementById('apply-coupon');

    if (!couponInput || !couponInput.value.trim()) {
        window.SeuConto.showToast('Digite um código de cupom', 'warning');
        return;
    }

    const couponCode = couponInput.value.trim().toUpperCase();

    window.SeuConto.setButtonLoading(couponButton);

    try {
        const response = await window.SeuConto.fetchAPI(window.BASE_PATH + '/api/checkout.php', {
            method: 'POST',
            body: JSON.stringify({
                action: 'validateCoupon',
                couponCode
            })
        });

        if (response.success) {
            window.SeuConto.showToast(
                `Cupom "${couponCode}" aplicado! Desconto: ${response.discount}`,
                'success'
            );

            // Atualiza preços com desconto
            // TODO: Implementar lógica de desconto

        } else {
            throw new Error(response.message || 'Cupom inválido');
        }

    } catch (error) {
        console.error('Erro ao aplicar cupom:', error);
        window.SeuConto.showToast(error.message, 'error');
    } finally {
        window.SeuConto.removeButtonLoading(couponButton);
    }
}

// ============================================
// VOLTAR PARA EDIÇÃO
// ============================================

/**
 * Volta para página de criação para editar
 */
function goBackToEdit() {
    if (confirm('Deseja voltar para editar os dados do livro?')) {
        window.location.href = window.BASE_PATH + '/pages/create/step1-theme.php';
    }
}

// ============================================
// ANIMAÇÃO DE LOADING
// ============================================

/**
 * Mostra animação de processamento
 */
function showProcessingAnimation() {
    const overlay = document.createElement('div');
    overlay.id = 'processing-overlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.95);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(8px);
    `;

    overlay.innerHTML = `
        <div style="text-align: center;">
            <div class="spinner" style="width: 60px; height: 60px; border-width: 5px; margin: 0 auto 2rem;"></div>
            <h3 style="font-family: var(--font-heading); font-size: 1.5rem; margin-bottom: 0.5rem;">
                Processando pagamento...
            </h3>
            <p style="color: var(--color-muted-foreground);">
                Aguarde enquanto redirecionamos você para a página segura de pagamento
            </p>
        </div>
    `;

    document.body.appendChild(overlay);
}

// ============================================
// PREVIEW DO TEMA
// ============================================

/**
 * Mostra preview do tema selecionado
 */
function showThemePreview() {
    const data = getStoredFormData();
    if (!data || !data.theme) return;

    const themeContainer = document.getElementById('theme-preview');
    if (!themeContainer) return;

    const themes = {
        coragem: {
            emoji: '🐉',
            name: 'Coragem',
            color: 'oklch(0.65 0.20 240)'
        },
        amizade: {
            emoji: '🤝',
            name: 'Amizade',
            color: 'oklch(0.75 0.18 340)'
        },
        exploracao: {
            emoji: '🦖',
            name: 'Exploração',
            color: 'oklch(0.70 0.18 145)'
        },
        magia: {
            emoji: '🧚',
            name: 'Magia',
            color: 'oklch(0.70 0.15 280)'
        }
    };

    const theme = themes[data.theme];
    if (!theme) return;

    themeContainer.innerHTML = `
        <div style="
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: ${theme.color};
            color: white;
            border-radius: var(--radius);
            font-weight: 600;
        ">
            <span style="font-size: 1.5rem;">${theme.emoji}</span>
            <span>${theme.name}</span>
        </div>
    `;
}

// ============================================
// INICIALIZAÇÃO
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    // Verifica se está na página de checkout
    const isCheckoutPage = document.getElementById('checkout-page');
    if (!isCheckoutPage) return;

    // Exibe resumo do pedido
    displayOrderSummary();

    // Exibe preview do tema
    showThemePreview();

    // Atualiza preços inicialmente
    updatePriceDisplay();

    // Event listener para checkbox de colorir
    const coloringCheckbox = document.getElementById('includeColoring');
    if (coloringCheckbox) {
        coloringCheckbox.addEventListener('change', updatePriceDisplay);
    }

    // Event listener para botão de checkout
    const checkoutButton = document.getElementById('checkout-button');
    if (checkoutButton) {
        checkoutButton.addEventListener('click', handleCheckout);
    }

    // Event listener para botão de voltar
    const backButton = document.getElementById('back-to-edit');
    if (backButton) {
        backButton.addEventListener('click', goBackToEdit);
    }

    // Event listener para cupom
    const applyCouponButton = document.getElementById('apply-coupon');
    if (applyCouponButton) {
        applyCouponButton.addEventListener('click', applyCoupon);
    }

    // Event listener para cópia física
    const physicalCopyButton = document.getElementById('request-physical-copy');
    if (physicalCopyButton) {
        physicalCopyButton.addEventListener('click', requestPhysicalCopy);
    }

    // Previne saída acidental
    let checkoutInProgress = false;

    checkoutButton?.addEventListener('click', () => {
        checkoutInProgress = true;
    });

    window.addEventListener('beforeunload', (e) => {
        if (!checkoutInProgress && hasValidFormData()) {
            e.preventDefault();
            e.returnValue = '';
            return 'Tem certeza que deseja sair? Seu progresso será perdido.';
        }
    });
});

// ============================================
// EXPORTAR FUNÇÕES
// ============================================
window.Checkout = {
    calculateTotal,
    handleCheckout,
    applyCoupon,
    requestPhysicalCopy,
    goBackToEdit
};
