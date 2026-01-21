<?php
/**
 * Header Component v2.0
 *
 * Navegacao principal com novo estilo visual
 * Bordas 3px, sombras fortes, mobile first
 */

// Verifica se variaveis de autenticacao estao definidas
$isLoggedIn = function_exists('isLoggedIn') ? isLoggedIn() : false;
$currentUser = function_exists('getCurrentUser') ? getCurrentUser() : null;

// Inclui paths.php se necessario para ter acesso a funcao url()
if (!function_exists('url')) {
    require_once __DIR__ . '/../config/paths.php';
}
?>
    <!-- Header -->
    <header class="header">
        <!-- Barra de Promo -->
        <div class="header-promo">
            🎁 Oferta de Lancamento: 40% OFF — Apenas R$ 29,90
        </div>

        <!-- Navegacao Principal -->
        <div class="header-main container">
            <!-- Logo -->
            <a href="<?php echo url('index.php'); ?>" class="logo">
                <div class="logo-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
                <span>Seu Conto</span>
            </a>

            <!-- Navegacao Desktop -->
            <nav class="nav-desktop">
                <a href="#como-funciona" class="nav-link" onclick="scrollToSection('como-funciona', event)">Como Funciona</a>
                <a href="#temas" class="nav-link" onclick="scrollToSection('temas', event)">Temas</a>
                <a href="#depoimentos" class="nav-link" onclick="scrollToSection('depoimentos', event)">Depoimentos</a>
                <a href="#faq" class="nav-link" onclick="scrollToSection('faq', event)">Duvidas</a>

                <?php if ($isLoggedIn): ?>
                    <!-- Usuario Logado -->
                    <div class="dropdown" id="user-dropdown">
                        <button onclick="toggleUserMenu()" class="user-avatar" style="cursor: pointer;">
                            <?php echo strtoupper(substr($currentUser['name'], 0, 1)); ?>
                        </button>

                        <!-- Menu Dropdown -->
                        <div id="user-menu" class="dropdown-menu">
                            <div style="padding: var(--space-md); border-bottom: 2px solid var(--color-border-light);">
                                <p style="font-weight: 700; margin-bottom: var(--space-xs);"><?php echo e($currentUser['name']); ?></p>
                                <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin: 0;"><?php echo e($currentUser['email']); ?></p>
                            </div>
                            <div style="padding: var(--space-sm);">
                                <a href="<?php echo url('pages/dashboard.php'); ?>" class="dropdown-item">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                    </svg>
                                    Dashboard
                                </a>
                                <div class="dropdown-divider"></div>
                                <button id="logout-button" class="dropdown-item" style="width: 100%; color: var(--color-error);">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    Sair
                                </button>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Usuario Nao Logado -->
                    <a href="<?php echo url('pages/auth/login.php'); ?>" class="btn btn-outline btn-sm">Entrar</a>
                    <a href="<?php echo url('pages/create/step1-theme.php'); ?>" class="btn btn-secondary btn-sm">Criar Meu Conto</a>
                <?php endif; ?>
            </nav>

            <!-- Menu Mobile Toggle -->
            <button id="mobile-menu-toggle" class="nav-mobile-toggle hide-desktop" aria-label="Menu">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Menu Mobile -->
        <nav id="mobile-menu" class="mobile-menu hidden">
            <div class="container">
                <div class="mobile-menu-links">
                    <a href="#como-funciona" class="mobile-menu-link" onclick="scrollToSection('como-funciona', event)">Como Funciona</a>
                    <a href="#temas" class="mobile-menu-link" onclick="scrollToSection('temas', event)">Temas</a>
                    <a href="#depoimentos" class="mobile-menu-link" onclick="scrollToSection('depoimentos', event)">Depoimentos</a>
                    <a href="#faq" class="mobile-menu-link" onclick="scrollToSection('faq', event)">Duvidas</a>
                </div>

                <div class="mobile-menu-actions">
                    <?php if ($isLoggedIn): ?>
                        <a href="<?php echo url('pages/dashboard.php'); ?>" class="btn btn-primary btn-full">Dashboard</a>
                        <button id="logout-button-mobile" class="btn btn-outline btn-full">Sair</button>
                    <?php else: ?>
                        <a href="<?php echo url('pages/auth/login.php'); ?>" class="btn btn-outline btn-full">Entrar</a>
                        <a href="<?php echo url('pages/create/step1-theme.php'); ?>" class="btn btn-secondary btn-full">Criar Meu Conto</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <?php
        // Exibe mensagem flash se existir
        if (function_exists('getFlashMessage')) {
            $flash = getFlashMessage();
            if ($flash):
        ?>
            <div class="container" style="padding-top: var(--space-lg);">
                <div class="toast toast-<?php echo $flash['type']; ?>" style="position: relative; animation: fadeIn 0.3s ease-out;">
                    <?php echo e($flash['message']); ?>
                </div>
            </div>
        <?php
            endif;
        }
        ?>

        <script>
            // Funcao para scroll suave em secoes
            function scrollToSection(sectionId, event) {
                if (event) event.preventDefault();

                // Fecha menu mobile se estiver aberto
                const mobileMenu = document.getElementById('mobile-menu');
                if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                }

                // Se nao estiver na home, redireciona com hash
                const basePath = '<?php echo BASE_PATH; ?>';
                const currentPath = window.location.pathname;
                const isHome = currentPath.endsWith('index.php') || currentPath === basePath + '/' || currentPath === basePath;

                if (!isHome) {
                    window.location.href = basePath + '/index.php#' + sectionId;
                    return;
                }

                const section = document.getElementById(sectionId);
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            // Toggle menu do usuario
            function toggleUserMenu() {
                const dropdown = document.getElementById('user-dropdown');
                if (dropdown) {
                    dropdown.classList.toggle('open');
                }
            }

            // Toggle menu mobile
            document.addEventListener('DOMContentLoaded', function() {
                const mobileToggle = document.getElementById('mobile-menu-toggle');
                const mobileMenu = document.getElementById('mobile-menu');

                if (mobileToggle && mobileMenu) {
                    mobileToggle.addEventListener('click', function() {
                        mobileMenu.classList.toggle('hidden');
                    });
                }
            });

            // Fecha menu ao clicar fora
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('user-dropdown');

                if (dropdown && !dropdown.contains(event.target)) {
                    dropdown.classList.remove('open');
                }
            });
        </script>
