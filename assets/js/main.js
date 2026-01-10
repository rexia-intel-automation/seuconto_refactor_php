/**
 * SEU CONTO - JavaScript Principal
 *
 * Funções globais e utilitários para toda a aplicação
 */

// ============================================
// CONSTANTES
// ============================================
const BASE_PATH = window.BASE_PATH || '';
const API_BASE_URL = BASE_PATH + '/api';
const TOAST_DURATION = 5000;

// ============================================
// UTILITÁRIOS GERAIS
// ============================================

/**
 * Faz uma requisição HTTP usando Fetch API
 * @param {string} url - URL da requisição
 * @param {Object} options - Opções do fetch
 * @returns {Promise<Object>} Resposta da API
 */
async function fetchAPI(url, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
        },
    };

    const config = { ...defaultOptions, ...options };

    try {
        const response = await fetch(url, config);
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Erro na requisição');
        }

        return data;
    } catch (error) {
        console.error('Erro na requisição:', error);
        throw error;
    }
}

/**
 * Exibe uma notificação toast
 * @param {string} message - Mensagem a exibir
 * @param {string} type - Tipo: 'success', 'error', 'warning', 'info'
 * @param {number} duration - Duração em ms
 */
function showToast(message, type = 'info', duration = TOAST_DURATION) {
    // Remove toast anterior se existir
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
        existingToast.remove();
    }

    // Cria novo toast
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;

    const icon = getToastIcon(type);

    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            ${icon}
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(toast);

    // Remove após duração
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

/**
 * Retorna o ícone SVG para o tipo de toast
 * @param {string} type - Tipo do toast
 * @returns {string} HTML do ícone
 */
function getToastIcon(type) {
    const icons = {
        success: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-success)"><polyline points="20 6 9 17 4 12"></polyline></svg>',
        error: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-error)"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',
        warning: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-warning)"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
        info: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-info)"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>'
    };
    return icons[type] || icons.info;
}

/**
 * Formata valor em centavos para Real brasileiro
 * @param {number} cents - Valor em centavos
 * @returns {string} Valor formatado (ex: "R$ 29,90")
 */
function formatPrice(cents) {
    const reais = cents / 100;
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(reais);
}

/**
 * Formata data para formato brasileiro
 * @param {string|Date} date - Data a formatar
 * @returns {string} Data formatada (ex: "10/01/2026")
 */
function formatDate(date) {
    const d = new Date(date);
    return new Intl.DateTimeFormat('pt-BR').format(d);
}

/**
 * Formata data com hora
 * @param {string|Date} date - Data a formatar
 * @returns {string} Data e hora formatadas
 */
function formatDateTime(date) {
    const d = new Date(date);
    return new Intl.DateTimeFormat('pt-BR', {
        dateStyle: 'short',
        timeStyle: 'short'
    }).format(d);
}

/**
 * Valida email
 * @param {string} email - Email a validar
 * @returns {boolean} True se válido
 */
function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Valida telefone brasileiro
 * @param {string} phone - Telefone a validar
 * @returns {boolean} True se válido
 */
function validatePhone(phone) {
    const cleaned = phone.replace(/\D/g, '');
    return cleaned.length >= 10 && cleaned.length <= 11;
}

/**
 * Formata telefone brasileiro
 * @param {string} phone - Telefone a formatar
 * @returns {string} Telefone formatado
 */
function formatPhone(phone) {
    const cleaned = phone.replace(/\D/g, '');

    if (cleaned.length === 11) {
        return cleaned.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (cleaned.length === 10) {
        return cleaned.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    }

    return phone;
}

/**
 * Debounce - Limita frequência de execução de função
 * @param {Function} func - Função a executar
 * @param {number} wait - Tempo de espera em ms
 * @returns {Function} Função com debounce
 */
function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Scroll suave para elemento
 * @param {string} elementId - ID do elemento
 */
function scrollToElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// ============================================
// MOBILE MENU
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.contains('hidden');

            if (isOpen) {
                mobileMenu.classList.remove('hidden');
                menuToggle.innerHTML = `
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                `;
            } else {
                mobileMenu.classList.add('hidden');
                menuToggle.innerHTML = `
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                `;
            }
        });

        // Fecha menu ao clicar em um link
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                menuToggle.innerHTML = `
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                `;
            });
        });
    }
});

// ============================================
// LOADING STATE
// ============================================

/**
 * Mostra loading em um botão
 * @param {HTMLElement} button - Elemento do botão
 */
function setButtonLoading(button) {
    if (!button) return;

    button.disabled = true;
    button.classList.add('loading');
    button.setAttribute('data-original-text', button.innerHTML);

    button.innerHTML = `
        <span class="spinner"></span>
        <span>Processando...</span>
    `;
}

/**
 * Remove loading de um botão
 * @param {HTMLElement} button - Elemento do botão
 */
function removeButtonLoading(button) {
    if (!button) return;

    button.disabled = false;
    button.classList.remove('loading');

    const originalText = button.getAttribute('data-original-text');
    if (originalText) {
        button.innerHTML = originalText;
        button.removeAttribute('data-original-text');
    }
}

// ============================================
// LOCAL STORAGE
// ============================================

/**
 * Salva dados no localStorage
 * @param {string} key - Chave
 * @param {any} data - Dados a salvar
 */
function saveToStorage(key, data) {
    try {
        localStorage.setItem(key, JSON.stringify(data));
    } catch (error) {
        console.error('Erro ao salvar no localStorage:', error);
    }
}

/**
 * Recupera dados do localStorage
 * @param {string} key - Chave
 * @returns {any} Dados recuperados ou null
 */
function getFromStorage(key) {
    try {
        const data = localStorage.getItem(key);
        return data ? JSON.parse(data) : null;
    } catch (error) {
        console.error('Erro ao recuperar do localStorage:', error);
        return null;
    }
}

/**
 * Remove dados do localStorage
 * @param {string} key - Chave
 */
function removeFromStorage(key) {
    try {
        localStorage.removeItem(key);
    } catch (error) {
        console.error('Erro ao remover do localStorage:', error);
    }
}

// ============================================
// MODAL
// ============================================

/**
 * Abre modal
 * @param {string} modalId - ID do modal
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Fecha modal
 * @param {string} modalId - ID do modal
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// Fecha modal ao clicar no overlay
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.add('hidden');
        document.body.style.overflow = '';
    }
});

// ============================================
// ANIMAÇÕES AO SCROLL
// ============================================

/**
 * Observer para animações ao scroll
 */
const observeElements = () => {
    const elements = document.querySelectorAll('.animate-on-scroll');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fadeIn');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    });

    elements.forEach(el => observer.observe(el));
};

// Executa quando DOM carregar
document.addEventListener('DOMContentLoaded', observeElements);

// ============================================
// CONTADOR DE CARACTERES
// ============================================

/**
 * Adiciona contador de caracteres a textarea
 * @param {HTMLElement} textarea - Elemento textarea
 * @param {number} maxLength - Comprimento máximo
 */
function addCharCounter(textarea, maxLength) {
    if (!textarea) return;

    const counter = document.createElement('div');
    counter.className = 'char-counter';
    counter.style.fontSize = '0.875rem';
    counter.style.color = 'var(--color-muted-foreground)';
    counter.style.marginTop = '0.25rem';
    counter.style.textAlign = 'right';

    const updateCounter = () => {
        const length = textarea.value.length;
        counter.textContent = `${length}/${maxLength}`;

        if (length > maxLength) {
            counter.style.color = 'var(--color-error)';
        } else {
            counter.style.color = 'var(--color-muted-foreground)';
        }
    };

    textarea.addEventListener('input', updateCounter);
    textarea.parentNode.appendChild(counter);
    updateCounter();
}

// ============================================
// COPIAR PARA ÁREA DE TRANSFERÊNCIA
// ============================================

/**
 * Copia texto para área de transferência
 * @param {string} text - Texto a copiar
 * @returns {Promise<boolean>} True se copiado com sucesso
 */
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        showToast('Copiado para área de transferência!', 'success', 2000);
        return true;
    } catch (error) {
        console.error('Erro ao copiar:', error);
        showToast('Erro ao copiar', 'error');
        return false;
    }
}

// ============================================
// EXPORTAR FUNÇÕES GLOBAIS
// ============================================
window.SeuConto = {
    fetchAPI,
    showToast,
    formatPrice,
    formatDate,
    formatDateTime,
    validateEmail,
    validatePhone,
    formatPhone,
    debounce,
    scrollToElement,
    setButtonLoading,
    removeButtonLoading,
    saveToStorage,
    getFromStorage,
    removeFromStorage,
    openModal,
    closeModal,
    addCharCounter,
    copyToClipboard
};
