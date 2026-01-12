/**
 * Image Comparison Slider
 *
 * Cria um slider de comparação "Antes/Depois" entre duas imagens
 * Usado na landing page para mostrar a transformação das fotos
 */

class ImageComparisonSlider {
    constructor(container) {
        this.container = typeof container === 'string'
            ? document.querySelector(container)
            : container;

        if (!this.container) {
            console.error('Container não encontrado para ImageComparisonSlider');
            return;
        }

        this.slider = null;
        this.beforeImage = null;
        this.afterImage = null;
        this.isDragging = false;
        this.currentPosition = 50; // Posição inicial em %

        this.init();
    }

    /**
     * Inicializa o slider
     */
    init() {
        // Encontrar elementos
        this.beforeImage = this.container.querySelector('.comparison-before');
        this.afterImage = this.container.querySelector('.comparison-after');
        this.slider = this.container.querySelector('.comparison-slider');

        if (!this.beforeImage || !this.afterImage || !this.slider) {
            console.error('Elementos necessários não encontrados no container');
            return;
        }

        // Configurar eventos
        this.setupEvents();

        // Posição inicial
        this.updatePosition(this.currentPosition);

        console.log('✅ Image Comparison Slider inicializado');
    }

    /**
     * Configurar event listeners
     */
    setupEvents() {
        // Mouse events
        this.slider.addEventListener('mousedown', this.startDrag.bind(this));
        document.addEventListener('mousemove', this.onDrag.bind(this));
        document.addEventListener('mouseup', this.stopDrag.bind(this));

        // Touch events (mobile)
        this.slider.addEventListener('touchstart', this.startDrag.bind(this), { passive: true });
        document.addEventListener('touchmove', this.onDrag.bind(this), { passive: false });
        document.addEventListener('touchend', this.stopDrag.bind(this));

        // Keyboard accessibility
        this.slider.addEventListener('keydown', this.onKeyDown.bind(this));

        // Tornar focusável
        this.slider.setAttribute('tabindex', '0');
        this.slider.setAttribute('role', 'slider');
        this.slider.setAttribute('aria-label', 'Comparação antes e depois');
        this.slider.setAttribute('aria-valuemin', '0');
        this.slider.setAttribute('aria-valuemax', '100');
        this.slider.setAttribute('aria-valuenow', this.currentPosition.toString());
    }

    /**
     * Iniciar arrasto
     */
    startDrag(e) {
        e.preventDefault();
        this.isDragging = true;
        this.slider.classList.add('dragging');

        // Focar no slider
        this.slider.focus();
    }

    /**
     * Parar arrasto
     */
    stopDrag() {
        if (this.isDragging) {
            this.isDragging = false;
            this.slider.classList.remove('dragging');
        }
    }

    /**
     * Durante o arrasto
     */
    onDrag(e) {
        if (!this.isDragging) return;

        e.preventDefault();

        // Obter posição do mouse/touch
        const containerRect = this.container.getBoundingClientRect();
        let clientX;

        if (e.type.includes('touch')) {
            clientX = e.touches[0].clientX;
        } else {
            clientX = e.clientX;
        }

        // Calcular posição relativa
        const x = clientX - containerRect.left;
        const percentage = (x / containerRect.width) * 100;

        // Limitar entre 0 e 100
        const clampedPercentage = Math.max(0, Math.min(100, percentage));

        this.updatePosition(clampedPercentage);
    }

    /**
     * Navegação por teclado
     */
    onKeyDown(e) {
        let newPosition = this.currentPosition;

        switch(e.key) {
            case 'ArrowLeft':
                newPosition -= 5;
                break;
            case 'ArrowRight':
                newPosition += 5;
                break;
            case 'Home':
                newPosition = 0;
                break;
            case 'End':
                newPosition = 100;
                break;
            default:
                return; // Não prevenir default para outras teclas
        }

        e.preventDefault();
        newPosition = Math.max(0, Math.min(100, newPosition));
        this.updatePosition(newPosition);
    }

    /**
     * Atualizar posição do slider
     */
    updatePosition(percentage) {
        this.currentPosition = percentage;

        // Atualizar clip-path da imagem "depois"
        this.afterImage.style.clipPath = `inset(0 ${100 - percentage}% 0 0)`;

        // Atualizar posição do slider
        this.slider.style.left = `${percentage}%`;

        // Atualizar aria-valuenow para acessibilidade
        this.slider.setAttribute('aria-valuenow', Math.round(percentage).toString());
    }

    /**
     * Animar para uma posição específica
     */
    animateTo(percentage, duration = 500) {
        const start = this.currentPosition;
        const distance = percentage - start;
        const startTime = performance.now();

        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            // Easing function (ease-in-out)
            const eased = progress < 0.5
                ? 2 * progress * progress
                : 1 - Math.pow(-2 * progress + 2, 2) / 2;

            const newPosition = start + (distance * eased);
            this.updatePosition(newPosition);

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    }

    /**
     * Reset para posição inicial (50%)
     */
    reset() {
        this.animateTo(50);
    }
}

/**
 * Auto-inicialização
 * Procura por todos os elementos com a classe .image-comparison
 */
function initComparisonSliders() {
    const sliders = document.querySelectorAll('.image-comparison');

    sliders.forEach(slider => {
        new ImageComparisonSlider(slider);
    });

    console.log(`✅ ${sliders.length} slider(s) de comparação inicializado(s)`);
}

// Inicializar quando o DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initComparisonSliders);
} else {
    initComparisonSliders();
}

// Exportar para uso global
window.ImageComparisonSlider = ImageComparisonSlider;
window.initComparisonSliders = initComparisonSliders;
