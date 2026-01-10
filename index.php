<?php
/**
 * Landing Page - Seu Conto
 *
 * Página inicial com apresentação do produto e CTAs
 */

// Configurações da página
$pageTitle = 'Seu Conto - Livros Infantis Personalizados com IA';
$additionalCSS = [];
$additionalJS = [];

// Inclui header
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="animate-on-scroll" style="padding: 3rem 0 5rem; background: radial-gradient(ellipse at top right, var(--color-accent) 0%, var(--color-background) 50%, var(--color-background) 100%);">
    <div class="container">
        <div style="text-align: center; max-width: 900px; margin: 0 auto;">
            <!-- Badge -->
            <div class="badge badge-primary animate-pulse" style="display: inline-flex; font-size: 0.875rem; margin-bottom: 1.5rem;">
                ✨ O PRESENTE MAIS MÁGICO DO ANO
            </div>

            <!-- Título Principal -->
            <h1 class="gradient-text" style="font-size: clamp(2rem, 5vw, 3.5rem); line-height: 1.1; margin-bottom: 1.5rem;">
                Sua criança VIRA o personagem de uma história mágica
            </h1>

            <p class="text-muted" style="font-size: 1.25rem; margin-bottom: 2.5rem; max-width: 700px; margin-left: auto; margin-right: auto;">
                Livros personalizados com IA que transformam fotos reais em ilustrações encantadoras. Entregue em até 30 minutos!
            </p>

            <!-- CTA Principal -->
            <a href="/refactor/pages/criar.php" class="btn btn-secondary btn-lg" style="border-radius: 9999px; padding: 1.25rem 3rem; font-size: 1.25rem; box-shadow: var(--shadow-lg);">
                Criar Meu Conto Agora →
            </a>

            <!-- Stats -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 2rem; margin-top: 4rem; text-align: center;">
                <div>
                    <p style="font-family: var(--font-heading); font-size: 2.5rem; font-weight: 700; color: var(--color-primary); margin-bottom: 0.25rem;">1000+</p>
                    <p class="text-muted" style="font-size: 0.875rem; margin: 0;">Livros Criados</p>
                </div>
                <div>
                    <p style="font-family: var(--font-heading); font-size: 2.5rem; font-weight: 700; color: var(--color-primary); margin-bottom: 0.25rem;">4.9/5</p>
                    <p class="text-muted" style="font-size: 0.875rem; margin: 0;">Avaliação Média</p>
                </div>
                <div>
                    <p style="font-family: var(--font-heading); font-size: 2.5rem; font-weight: 700; color: var(--color-primary); margin-bottom: 0.25rem;">30min</p>
                    <p class="text-muted" style="font-size: 0.875rem; margin: 0;">Tempo de Entrega</p>
                </div>
            </div>
        </div>
    </div>
</section>

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
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--color-success), oklch(0.65 0.18 155)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 12 20 22 4 22 4 12"></polyline>
                        <rect x="2" y="7" width="20" height="5"></rect>
                        <line x1="12" y1="22" x2="12" y2="7"></line>
                        <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
                        <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
                    </svg>
                </div>
                <h3 style="margin-bottom: 1rem;">3. Receba na Hora</h3>
                <p class="text-muted">Baixe o PDF em até 30 minutos direto no seu email</p>
            </div>
        </div>
    </div>
</section>

<!-- Temas -->
<section id="temas" class="animate-on-scroll" style="padding: 5rem 0; background: var(--color-muted);">
    <div class="container">
        <div style="text-align: center; margin-bottom: 4rem;">
            <h2 style="font-size: clamp(2rem, 4vw, 2.5rem); margin-bottom: 1rem;">Escolha o Tema Perfeito</h2>
            <p class="text-muted" style="font-size: 1.125rem;">4 temas mágicos criados especialmente para crianças</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 2rem;">
            <!-- Coragem -->
            <div class="card" style="background: linear-gradient(135deg, oklch(0.95 0.05 240), white); border: 2px solid oklch(0.65 0.20 240);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🐉</div>
                <h3 style="color: oklch(0.50 0.25 240); margin-bottom: 0.75rem;">Coragem</h3>
                <p class="text-muted" style="font-size: 0.875rem;">Histórias de bravura e superação de medos</p>
            </div>

            <!-- Amizade -->
            <div class="card" style="background: linear-gradient(135deg, oklch(0.95 0.05 340), white); border: 2px solid oklch(0.75 0.18 340);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🤝</div>
                <h3 style="color: oklch(0.60 0.20 340); margin-bottom: 0.75rem;">Amizade</h3>
                <p class="text-muted" style="font-size: 0.875rem;">Aventuras sobre companheirismo e lealdade</p>
            </div>

            <!-- Exploração -->
            <div class="card" style="background: linear-gradient(135deg, oklch(0.95 0.05 145), white); border: 2px solid oklch(0.70 0.18 145);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🦖</div>
                <h3 style="color: oklch(0.50 0.20 145); margin-bottom: 0.75rem;">Exploração</h3>
                <p class="text-muted" style="font-size: 0.875rem;">Descobertas e aventuras pelo desconhecido</p>
            </div>

            <!-- Magia -->
            <div class="card" style="background: linear-gradient(135deg, var(--color-accent), white); border: 2px solid var(--color-primary);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🧚</div>
                <h3 style="color: var(--color-primary); margin-bottom: 0.75rem;">Magia</h3>
                <p class="text-muted" style="font-size: 0.875rem;">Mundos encantados cheios de mistério</p>
            </div>
        </div>
    </div>
</section>

<!-- Depoimentos -->
<section id="depoimentos" class="animate-on-scroll" style="padding: 5rem 0;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 4rem;">
            <h2 style="font-size: clamp(2rem, 4vw, 2.5rem); margin-bottom: 1rem;">O que dizem as famílias</h2>
            <p class="text-muted" style="font-size: 1.125rem;">Pais e mães encantados com nossos livros</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div class="card">
                <div style="color: var(--color-secondary); margin-bottom: 1rem;">⭐⭐⭐⭐⭐</div>
                <p style="margin-bottom: 1.5rem;">"Minha filha não para de ler! É incrível ver ela como personagem da própria história. Melhor presente que já dei!"</p>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; background: var(--color-accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--color-primary);">MR</div>
                    <div>
                        <p style="font-weight: 600; margin: 0;">Maria Rodrigues</p>
                        <p class="text-muted" style="font-size: 0.875rem; margin: 0;">Mãe da Sofia, 7 anos</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div style="color: var(--color-secondary); margin-bottom: 1rem;">⭐⭐⭐⭐⭐</div>
                <p style="margin-bottom: 1.5rem;">"Qualidade impecável! As ilustrações são lindas e a história é muito bem escrita. Recomendo demais!"</p>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; background: var(--color-accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--color-primary);">JS</div>
                    <div>
                        <p style="font-weight: 600; margin: 0;">João Silva</p>
                        <p class="text-muted" style="font-size: 0.875rem; margin: 0;">Pai do Miguel, 5 anos</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div style="color: var(--color-secondary); margin-bottom: 1rem;">⭐⭐⭐⭐⭐</div>
                <p style="margin-bottom: 1.5rem;">"Chegou super rápido! Em menos de 30 minutos já estava lendo com meu filho. Ele amou se ver no livro!"</p>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; background: var(--color-accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--color-primary);">AC</div>
                    <div>
                        <p style="font-weight: 600; margin: 0;">Ana Costa</p>
                        <p class="text-muted" style="font-size: 0.875rem; margin: 0;">Mãe do Pedro, 4 anos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section id="faq" class="animate-on-scroll" style="padding: 5rem 0; background: var(--color-muted);">
    <div class="container" style="max-width: 800px;">
        <div style="text-align: center; margin-bottom: 4rem;">
            <h2 style="font-size: clamp(2rem, 4vw, 2.5rem); margin-bottom: 1rem;">Perguntas Frequentes</h2>
            <p class="text-muted" style="font-size: 1.125rem;">Tire suas dúvidas sobre o Seu Conto</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <details class="card" style="cursor: pointer;">
                <summary style="font-weight: 600; font-size: 1.125rem;">Como funciona a personalização com IA?</summary>
                <p class="text-muted" style="margin-top: 1rem; padding-left: 1rem;">Nossa inteligência artificial analisa a foto da criança e cria ilustrações únicas que mantêm suas características principais. A história também é personalizada com o nome e preferências informadas.</p>
            </details>

            <details class="card" style="cursor: pointer;">
                <summary style="font-weight: 600; font-size: 1.125rem;">Quanto tempo leva para receber o livro?</summary>
                <p class="text-muted" style="margin-top: 1rem; padding-left: 1rem;">O livro digital (PDF) é entregue em até 30 minutos após a confirmação do pagamento, diretamente no seu email ou WhatsApp.</p>
            </details>

            <details class="card" style="cursor: pointer;">
                <summary style="font-weight: 600; font-size: 1.125rem;">Posso imprimir o livro?</summary>
                <p class="text-muted" style="margin-top: 1rem; padding-left: 1rem;">Sim! O PDF tem alta qualidade e pode ser impresso em qualquer gráfica. Em breve teremos a opção de envio de cópias físicas impressas.</p>
            </details>

            <details class="card" style="cursor: pointer;">
                <summary style="font-weight: 600; font-size: 1.125rem;">Meus dados estão seguros?</summary>
                <p class="text-muted" style="margin-top: 1rem; padding-left: 1rem;">Sim! Usamos criptografia SSL e processamento de pagamento via Stripe. Suas fotos e dados são tratados com total segurança e privacidade.</p>
            </details>
        </div>
    </div>
</section>

<!-- CTA Final -->
<section class="animate-on-scroll" style="padding: 5rem 0;">
    <div class="container">
        <div class="gradient-primary" style="border-radius: var(--radius-xl); padding: 4rem 2rem; text-align: center; color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -100px; right: -100px; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255, 255, 255, 0.1), transparent); border-radius: 50%;"></div>
            <h2 style="font-size: clamp(2rem, 4vw, 3rem); margin-bottom: 1.5rem; position: relative; z-index: 1;">Pronto para criar a história mágica?</h2>
            <p style="font-size: 1.25rem; margin-bottom: 2.5rem; opacity: 0.95; position: relative; z-index: 1; max-width: 600px; margin-left: auto; margin-right: auto;">Comece agora e veja a alegria no rosto da criança ao se tornar o herói da própria aventura!</p>
            <a href="/refactor/pages/criar.php" class="btn btn-lg" style="background: white; color: var(--color-primary); border-radius: 9999px; padding: 1.25rem 3rem; font-size: 1.25rem; position: relative; z-index: 1;">Criar Meu Conto →</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
