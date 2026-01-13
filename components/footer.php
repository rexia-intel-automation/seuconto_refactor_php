<?php
/**
 * Footer Component v2.0
 *
 * Rodape do site com novo estilo visual
 * Bordas 3px, sombras fortes, mobile first
 */

// Inclui paths.php se necessario para ter acesso a funcao url() e asset()
if (!function_exists('url')) {
    require_once __DIR__ . '/../config/paths.php';
}
?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Coluna 1: Brand -->
                <div class="footer-brand">
                    <a href="<?php echo url('index.php'); ?>" class="logo">
                        <div class="logo-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                        </div>
                        <span>Seu Conto</span>
                    </a>
                    <p>
                        Transformamos criancas em protagonistas de historias magicas criadas com inteligencia artificial. Cada livro e unico e especial.
                    </p>

                    <!-- Social Links -->
                    <div class="footer-social">
                        <a href="https://instagram.com/seuconto" target="_blank" rel="noopener" aria-label="Instagram">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                        </a>
                        <a href="https://facebook.com/seuconto" target="_blank" rel="noopener" aria-label="Facebook">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                            </svg>
                        </a>
                        <a href="mailto:contato@seuconto.com.br" aria-label="Email">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Coluna 2: Links Rapidos -->
                <div class="footer-column">
                    <h4>Links Rapidos</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo url('index.php'); ?>">Inicio</a></li>
                        <li><a href="<?php echo url('pages/create/step1-theme.php'); ?>">Criar Meu Conto</a></li>
                        <li><a href="<?php echo url('index.php#temas'); ?>">Ver Temas</a></li>
                        <li><a href="<?php echo url('index.php#como-funciona'); ?>">Como Funciona</a></li>
                        <li><a href="<?php echo url('index.php#depoimentos'); ?>">Depoimentos</a></li>
                        <li><a href="<?php echo url('index.php#faq'); ?>">Perguntas Frequentes</a></li>
                    </ul>
                </div>

                <!-- Coluna 3: Legal -->
                <div class="footer-column">
                    <h4>Legal</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo url('pages/legal/terms.php'); ?>">Termos de Uso</a></li>
                        <li><a href="<?php echo url('pages/legal/privacy.php'); ?>">Politica de Privacidade</a></li>
                    </ul>
                </div>

                <!-- Coluna 4: Contato & Trust -->
                <div class="footer-column">
                    <h4>Contato</h4>
                    <ul class="footer-links">
                        <li style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <a href="mailto:contato@seuconto.com.br">contato@seuconto.com.br</a>
                        </li>
                    </ul>

                    <!-- Trust Badges -->
                    <div style="margin-top: var(--space-lg); padding-top: var(--space-lg); border-top: 2px dashed var(--color-border-light);">
                        <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: var(--space-sm); color: var(--color-foreground);">
                            Compra 100% Segura
                        </p>
                        <div style="display: flex; flex-wrap: wrap; gap: var(--space-sm);">
                            <div class="badge badge-success">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                    <path d="m9 12 2 2 4-4"></path>
                                </svg>
                                SSL
                            </div>
                            <div class="badge badge-info">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                Criptografado
                            </div>
                            <div class="badge badge-primary">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                    <line x1="1" y1="10" x2="23" y2="10"></line>
                                </svg>
                                Stripe
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <p style="margin-bottom: var(--space-xs);">
                    &copy; <?php echo date('Y'); ?> Seu Conto. Todos os direitos reservados.
                </p>
                <p style="margin: 0; font-size: 0.75rem;">
                    Feito com ❤️ para familias
                </p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/5511999999999?text=Ola!%20Gostaria%20de%20saber%20mais%20sobre%20o%20Seu%20Conto" target="_blank" rel="noopener" class="whatsapp-float" aria-label="Fale conosco no WhatsApp" style="position: fixed; bottom: var(--space-lg); right: var(--space-lg); width: 56px; height: 56px; background: #25D366; border: var(--border-width) solid #128C7E; border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-lg); z-index: 1000; transition: all var(--transition-base); text-decoration: none;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="white">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>

    <style>
        .whatsapp-float:hover {
            transform: translate(-4px, -4px);
            box-shadow: var(--shadow-xl);
        }
        .whatsapp-float:active {
            transform: translate(2px, 2px);
            box-shadow: var(--shadow-sm);
        }
        @media (max-width: 639px) {
            .whatsapp-float {
                bottom: var(--space-md) !important;
                right: var(--space-md) !important;
                width: 48px !important;
                height: 48px !important;
            }
            .whatsapp-float svg {
                width: 24px;
                height: 24px;
            }
        }
    </style>

    <!-- JavaScript Global (BASE_PATH definido no head.php) -->
    <script src="<?php echo asset('js/main.js'); ?>"></script>
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Inline Scripts (se houver) -->
    <?php if (isset($inlineScripts)): ?>
        <?php foreach ($inlineScripts as $script): ?>
            <script><?php echo $script; ?></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
