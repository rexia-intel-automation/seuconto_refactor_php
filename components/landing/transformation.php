<!-- Transformação Antes/Depois -->
<section class="animate-on-scroll" style="padding: 5rem 0; background: var(--color-muted);">
    <div class="container">
        <div style="text-align: center; margin-bottom: 4rem;">
            <h2 style="font-size: clamp(2rem, 4vw, 2.5rem); margin-bottom: 1rem;">A Transformação é Mágica</h2>
            <p class="text-muted" style="font-size: 1.125rem;">Veja como transformamos fotos reais em ilustrações incríveis</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem; max-width: 1000px; margin: 0 auto;">
            <!-- Antes/Depois 1 -->
            <div class="card">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <p style="text-align: center; font-size: 0.875rem; font-weight: 600; color: var(--color-muted-foreground); margin-bottom: 0.75rem;">FOTO ORIGINAL</p>
                        <div style="aspect-ratio: 1; background: var(--color-border); border-radius: var(--radius); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" opacity="0.3">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p style="text-align: center; font-size: 0.875rem; font-weight: 600; color: var(--color-primary); margin-bottom: 0.75rem;">ILUSTRAÇÃO IA</p>
                        <div style="aspect-ratio: 1; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); border-radius: var(--radius); display: flex; align-items: center; justify-content: center;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
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
                        </div>
                    </div>
                </div>
                <p style="text-align: center; color: var(--color-muted-foreground); font-size: 0.875rem;">Tema: Aventura Espacial</p>
            </div>

            <!-- Antes/Depois 2 -->
            <div class="card">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <p style="text-align: center; font-size: 0.875rem; font-weight: 600; color: var(--color-muted-foreground); margin-bottom: 0.75rem;">FOTO ORIGINAL</p>
                        <div style="aspect-ratio: 1; background: var(--color-border); border-radius: var(--radius); display: flex; align-items: center; justify-content: center;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" opacity="0.3">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p style="text-align: center; font-size: 0.875rem; font-weight: 600; color: var(--color-primary); margin-bottom: 0.75rem;">ILUSTRAÇÃO IA</p>
                        <div style="aspect-ratio: 1; background: linear-gradient(135deg, #F472B6, #A78BFA); border-radius: var(--radius); display: flex; align-items: center; justify-content: center;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
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
                        </div>
                    </div>
                </div>
                <p style="text-align: center; color: var(--color-muted-foreground); font-size: 0.875rem;">Tema: Reino de Fantasia</p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?php echo url('pages/criar.php'); ?>" class="btn btn-primary btn-lg">
                Ver Mais Exemplos →
            </a>
        </div>
    </div>
</section>
