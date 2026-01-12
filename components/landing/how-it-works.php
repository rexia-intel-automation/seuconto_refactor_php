<!-- Como Funciona -->
<section id="como-funciona" class="animate-on-scroll" style="padding: 5rem 0;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 4rem;">
            <h2 style="font-size: clamp(2rem, 4vw, 2.5rem); margin-bottom: 1rem;">Como Funciona?</h2>
            <p class="text-muted" style="font-size: 1.125rem;">3 passos simples para criar a história mágica</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 3rem;">
            <!-- Passo 1 -->
            <div class="card" style="text-align: center;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--color-primary), oklch(0.60 0.20 280)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                </div>
                <h3 style="margin-bottom: 1rem;">1. Envie uma Foto</h3>
                <p class="text-muted">Faça upload de uma foto da criança e conte um pouco sobre ela</p>
            </div>

            <!-- Passo 2 -->
            <div class="card" style="text-align: center;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--color-secondary), oklch(0.78 0.20 75)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                <h3 style="margin-bottom: 1rem;">2. A Magia Acontece</h3>
                <p class="text-muted">Nossa IA cria ilustrações únicas e uma história personalizada</p>
            </div>

            <!-- Passo 3 -->
            <div class="card" style="text-align: center;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #34D399, #10B981); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        <line x1="10" y1="8" x2="16" y2="8"></line>
                        <line x1="10" y1="12" x2="16" y2="12"></line>
                        <line x1="10" y1="16" x2="13" y2="16"></line>
                    </svg>
                </div>
                <h3 style="margin-bottom: 1rem;">3. Receba o E-book</h3>
                <p class="text-muted">Baixe o PDF em até 30 minutos e imprima ou leia no celular</p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?php echo url('pages/create/step1-theme.php'); ?>" class="btn btn-secondary btn-lg" style="border-radius: 9999px;">
                Começar Agora →
            </a>
        </div>
    </div>
</section>
