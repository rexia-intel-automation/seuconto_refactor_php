/**
 * SEU CONTO - JavaScript de Autenticação
 *
 * Funcionalidades de login e registro
 */

// ============================================
// VALIDAÇÕES
// ============================================

/**
 * Valida força da senha
 * @param {string} password - Senha a validar
 * @returns {Object} Objeto com strength e feedback
 */
function validatePasswordStrength(password) {
    const result = {
        strength: 'weak',
        score: 0,
        feedback: []
    };

    if (password.length < 6) {
        result.feedback.push('Mínimo de 6 caracteres');
        return result;
    }

    // Critérios
    const criteria = {
        length: password.length >= 8,
        lowercase: /[a-z]/.test(password),
        uppercase: /[A-Z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };

    // Calcula pontuação
    const score = Object.values(criteria).filter(Boolean).length;
    result.score = score;

    // Determina força
    if (score >= 4) {
        result.strength = 'strong';
    } else if (score >= 2) {
        result.strength = 'medium';
    } else {
        result.strength = 'weak';
    }

    // Feedback
    if (!criteria.length) result.feedback.push('Use pelo menos 8 caracteres');
    if (!criteria.uppercase) result.feedback.push('Adicione letras maiúsculas');
    if (!criteria.lowercase) result.feedback.push('Adicione letras minúsculas');
    if (!criteria.number) result.feedback.push('Adicione números');
    if (!criteria.special) result.feedback.push('Adicione caracteres especiais');

    return result;
}

/**
 * Atualiza UI de força da senha
 * @param {string} password - Senha
 * @param {HTMLElement} container - Container do indicador
 */
function updatePasswordStrengthUI(password, container) {
    if (!container) return;

    const { strength, feedback } = validatePasswordStrength(password);

    const fillElement = container.querySelector('.strength-fill');
    const textElement = container.querySelector('.strength-text');

    if (fillElement) {
        fillElement.className = `strength-fill ${strength}`;
    }

    if (textElement) {
        const strengthLabels = {
            weak: 'Fraca',
            medium: 'Média',
            strong: 'Forte'
        };
        textElement.className = `strength-text ${strength}`;
        textElement.textContent = strengthLabels[strength];
    }
}

// ============================================
// REGISTRO
// ============================================

/**
 * Manipula submissão do formulário de registro
 */
async function handleRegisterSubmit(event) {
    event.preventDefault();

    const form = event.target;
    const submitButton = form.querySelector('button[type="submit"]');

    // Coleta dados do formulário
    const formData = {
        fullName: form.fullName.value.trim(),
        email: form.email.value.trim(),
        phone: form.phone.value.trim(),
        password: form.password.value,
        confirmPassword: form.confirmPassword?.value,
        terms: form.terms?.checked
    };

    // Validações
    const errors = [];

    if (!formData.fullName || formData.fullName.length < 3) {
        errors.push('Nome completo deve ter pelo menos 3 caracteres');
    }

    if (!window.SeuConto.validateEmail(formData.email)) {
        errors.push('Email inválido');
    }

    if (!window.SeuConto.validatePhone(formData.phone)) {
        errors.push('Telefone/WhatsApp inválido');
    }

    const passwordCheck = validatePasswordStrength(formData.password);
    if (passwordCheck.strength === 'weak') {
        errors.push('Senha muito fraca. ' + passwordCheck.feedback.join(', '));
    }

    if (formData.confirmPassword && formData.password !== formData.confirmPassword) {
        errors.push('As senhas não coincidem');
    }

    if (form.terms && !formData.terms) {
        errors.push('Você deve aceitar os termos de uso');
    }

    // Mostra erros
    if (errors.length > 0) {
        showFormErrors(errors);
        return;
    }

    // Limpa erros anteriores
    clearFormErrors();

    // Ativa loading
    window.SeuConto.setButtonLoading(submitButton);

    try {
        const response = await window.SeuConto.fetchAPI('/refactor/api/auth.php', {
            method: 'POST',
            body: JSON.stringify({
                action: 'register',
                ...formData
            })
        });

        if (response.success) {
            window.SeuConto.showToast(
                'Conta criada com sucesso! Redirecionando...',
                'success'
            );

            // Redireciona após 1 segundo
            setTimeout(() => {
                window.location.href = '/refactor/pages/dashboard.php';
            }, 1000);
        } else {
            throw new Error(response.message || 'Erro ao criar conta');
        }

    } catch (error) {
        console.error('Erro no registro:', error);
        window.SeuConto.showToast(error.message, 'error');
        window.SeuConto.removeButtonLoading(submitButton);
    }
}

// ============================================
// LOGIN
// ============================================

/**
 * Manipula submissão do formulário de login
 */
async function handleLoginSubmit(event) {
    event.preventDefault();

    const form = event.target;
    const submitButton = form.querySelector('button[type="submit"]');

    // Coleta dados
    const formData = {
        email: form.email.value.trim(),
        password: form.password.value,
        remember: form.remember?.checked || false
    };

    // Validações básicas
    const errors = [];

    if (!window.SeuConto.validateEmail(formData.email)) {
        errors.push('Email inválido');
    }

    if (!formData.password || formData.password.length < 6) {
        errors.push('Senha deve ter pelo menos 6 caracteres');
    }

    if (errors.length > 0) {
        showFormErrors(errors);
        return;
    }

    clearFormErrors();

    // Ativa loading
    window.SeuConto.setButtonLoading(submitButton);

    try {
        const response = await window.SeuConto.fetchAPI('/refactor/api/auth.php', {
            method: 'POST',
            body: JSON.stringify({
                action: 'login',
                ...formData
            })
        });

        if (response.success) {
            window.SeuConto.showToast('Login realizado com sucesso!', 'success');

            // Redireciona
            const redirectUrl = new URLSearchParams(window.location.search).get('redirect')
                || '/refactor/pages/dashboard.php';

            setTimeout(() => {
                window.location.href = redirectUrl;
            }, 500);
        } else {
            throw new Error(response.message || 'Email ou senha incorretos');
        }

    } catch (error) {
        console.error('Erro no login:', error);
        window.SeuConto.showToast(error.message, 'error');
        window.SeuConto.removeButtonLoading(submitButton);
    }
}

// ============================================
// LOGOUT
// ============================================

/**
 * Realiza logout do usuário
 */
async function handleLogout() {
    try {
        const response = await window.SeuConto.fetchAPI('/refactor/api/auth.php', {
            method: 'POST',
            body: JSON.stringify({ action: 'logout' })
        });

        if (response.success) {
            window.SeuConto.showToast('Logout realizado com sucesso', 'success');
            setTimeout(() => {
                window.location.href = '/refactor/index.php';
            }, 500);
        }
    } catch (error) {
        console.error('Erro no logout:', error);
        window.SeuConto.showToast('Erro ao fazer logout', 'error');
    }
}

// ============================================
// UI HELPERS
// ============================================

/**
 * Mostra erros no formulário
 * @param {Array<string>} errors - Array de mensagens de erro
 */
function showFormErrors(errors) {
    clearFormErrors();

    const alertContainer = document.getElementById('form-alert');
    if (!alertContainer) return;

    const errorList = errors.map(error => `<li>${error}</li>`).join('');

    alertContainer.innerHTML = `
        <div class="auth-alert auth-alert-error">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            <div>
                <ul style="margin: 0; padding-left: 1.25rem;">
                    ${errorList}
                </ul>
            </div>
        </div>
    `;

    alertContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/**
 * Limpa erros do formulário
 */
function clearFormErrors() {
    const alertContainer = document.getElementById('form-alert');
    if (alertContainer) {
        alertContainer.innerHTML = '';
    }
}

/**
 * Valida campo em tempo real
 * @param {HTMLInputElement} input - Campo de input
 * @param {Function} validator - Função de validação
 */
function setupRealtimeValidation(input, validator) {
    if (!input) return;

    const validate = window.SeuConto.debounce(() => {
        const isValid = validator(input.value);

        if (input.value.length > 0) {
            input.classList.toggle('valid', isValid);
            input.classList.toggle('invalid', !isValid);
        } else {
            input.classList.remove('valid', 'invalid');
        }
    }, 300);

    input.addEventListener('input', validate);
    input.addEventListener('blur', validate);
}

/**
 * Alterna visibilidade da senha
 * @param {string} inputId - ID do campo de senha
 * @param {HTMLElement} toggleButton - Botão de toggle
 */
function togglePasswordVisibility(inputId, toggleButton) {
    const input = document.getElementById(inputId);
    if (!input || !toggleButton) return;

    const type = input.type === 'password' ? 'text' : 'password';
    input.type = type;

    // Atualiza ícone
    const icon = type === 'password' ? 'eye' : 'eye-off';
    toggleButton.innerHTML = getEyeIcon(icon);
}

/**
 * Retorna SVG do ícone de olho
 * @param {string} type - 'eye' ou 'eye-off'
 * @returns {string} HTML do SVG
 */
function getEyeIcon(type) {
    if (type === 'eye') {
        return `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        `;
    } else {
        return `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
        `;
    }
}

// ============================================
// MÁSCARA DE TELEFONE
// ============================================

/**
 * Aplica máscara de telefone brasileiro
 * @param {HTMLInputElement} input - Campo de telefone
 */
function applyPhoneMask(input) {
    if (!input) return;

    input.addEventListener('input', (e) => {
        let value = e.target.value.replace(/\D/g, '');

        if (value.length > 11) {
            value = value.slice(0, 11);
        }

        if (value.length >= 11) {
            value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        } else if (value.length >= 10) {
            value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else if (value.length >= 6) {
            value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else if (value.length >= 2) {
            value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
        }

        e.target.value = value;
    });
}

// ============================================
// INICIALIZAÇÃO
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    // Formulário de registro
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegisterSubmit);

        // Validação em tempo real
        const emailInput = registerForm.querySelector('input[name="email"]');
        const phoneInput = registerForm.querySelector('input[name="phone"]');
        const passwordInput = registerForm.querySelector('input[name="password"]');

        if (emailInput) {
            setupRealtimeValidation(emailInput, window.SeuConto.validateEmail);
        }

        if (phoneInput) {
            applyPhoneMask(phoneInput);
            setupRealtimeValidation(phoneInput, window.SeuConto.validatePhone);
        }

        if (passwordInput) {
            const strengthContainer = document.querySelector('.password-strength');
            if (strengthContainer) {
                passwordInput.addEventListener('input', () => {
                    updatePasswordStrengthUI(passwordInput.value, strengthContainer);
                });
            }
        }
    }

    // Formulário de login
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLoginSubmit);
    }

    // Botões de toggle de senha
    const passwordToggles = document.querySelectorAll('[data-toggle-password]');
    passwordToggles.forEach(toggle => {
        const targetId = toggle.getAttribute('data-toggle-password');
        toggle.addEventListener('click', () => {
            togglePasswordVisibility(targetId, toggle);
        });
    });

    // Botão de logout
    const logoutButton = document.getElementById('logout-button');
    if (logoutButton) {
        logoutButton.addEventListener('click', (e) => {
            e.preventDefault();
            if (confirm('Tem certeza que deseja sair?')) {
                handleLogout();
            }
        });
    }
});
