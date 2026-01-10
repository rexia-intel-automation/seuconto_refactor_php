<?php
/**
 * Header Global
 *
 * Componente de cabeçalho usado em todas as páginas
 */

// Inclui sessão se não estiver incluída
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/session.php';
}

$currentUser = getCurrentUser();
$isLoggedIn = isLoggedIn();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Transforme sua criança no protagonista de uma história mágica personalizada com inteligência artificial">
    <meta name="keywords" content="livros personalizados, histórias infantis, IA, presente criativo">
    <meta name="author" content="Seu Conto">

    <title><?php echo $pageTitle ?? 'Seu Conto - Livros Infantis Personalizados com IA'; ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/refactor/assets/images/favicon.svg">

    <!-- CSS -->
    <link rel="stylesheet" href="/refactor/assets/css/main.css">
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Preconnect para fontes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <!-- Barra de Promo -->
        <div class="header-promo animate-pulse">
            🎁 Oferta de Lançamento: 40% OFF — Apenas R$ 29,90
        </div>

        <!-- Navegação Principal -->
        <div class="header-main container">
            <!-- Logo -->
            <a href="/refactor/index.php" class="logo">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <span>Seu Conto</span>
            </a>

            <!-- Navegação Desktop -->
            <nav class="nav-desktop">
                <a href="#como-funciona" class="nav-link" onclick="scrollToSection('como-funciona', event)">Como Funciona</a>
                <a href="#temas" class="nav-link" onclick="scrollToSection('temas', event)">Temas</a>
                <a href="#depoimentos" class="nav-link" onclick="scrollToSection('depoimentos', event)">Depoimentos</a>
                <a href="#faq" class="nav-link" onclick="scrollToSection('faq', event)">Dúvidas</a>

                <?php if ($isLoggedIn): ?>
                    <!-- Usuário Logado -->
                    <div style="position: relative;">
                        <button onclick="toggleUserMenu()" class="user-avatar" style="cursor: pointer; border: 2px solid var(--color-primary);">
                            <?php echo strtoupper(substr($currentUser['name'], 0, 1)); ?>
                        </button>

                        <!-- Menu Dropdown -->
                        <div id="user-menu" class="hidden" style="position: absolute; right: 0; top: calc(100% + 0.5rem); background: var(--color-card); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); min-width: 200px; z-index: 1000;">
                            <div style="padding: 1rem; border-bottom: 1px solid var(--color-border);">
                                <p style="font-weight: 600; margin-bottom: 0.25rem;"><?php echo e($currentUser['name']); ?></p>
                                <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin: 0;"><?php echo e($currentUser['email']); ?></p>
                            </div>
                            <div style="padding: 0.5rem;">
                                <a href="/refactor/pages/dashboard.php" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border-radius: var(--radius); color: var(--color-foreground); transition: background var(--transition-fast); text-decoration: none;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                    </svg>
                                    Dashboard
                                </a>
                                <button id="logout-button" style="width: 100%; display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border-radius: var(--radius); color: var(--color-error); background: transparent; border: none; cursor: pointer; transition: background var(--transition-fast); font-family: var(--font-body);">
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
                    <!-- Usuário Não Logado -->
                    <a href="/refactor/pages/auth/login.php" class="btn btn-outline btn-sm">Entrar</a>
                    <a href="/refactor/pages/criar.php" class="btn btn-secondary btn-sm" style="border-radius: 9999px;">Criar Meu Conto</a>
                <?php endif; ?>
            </nav>

            <!-- Menu Mobile Toggle -->
            <button id="mobile-menu-toggle" class="md-hidden" style="background: none; border: none; cursor: pointer; color: var(--color-foreground);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Menu Mobile -->
        <nav id="mobile-menu" class="hidden md-hidden" style="background: var(--color-card); border-top: 1px solid var(--color-border); padding: 1.5rem;">
            <div class="container">
                <a href="#como-funciona" class="nav-link" style="display: block; padding: 0.75rem 0; border-bottom: 1px solid var(--color-border);" onclick="scrollToSection('como-funciona', event)">Como Funciona</a>
                <a href="#temas" class="nav-link" style="display: block; padding: 0.75rem 0; border-bottom: 1px solid var(--color-border);" onclick="scrollToSection('temas', event)">Temas</a>
                <a href="#depoimentos" class="nav-link" style="display: block; padding: 0.75rem 0; border-bottom: 1px solid var(--color-border);" onclick="scrollToSection('depoimentos', event)">Depoimentos</a>
                <a href="#faq" class="nav-link" style="display: block; padding: 0.75rem 0; border-bottom: 1px solid var(--color-border);" onclick="scrollToSection('faq', event)">Dúvidas</a>

                <?php if ($isLoggedIn): ?>
                    <a href="/refactor/pages/dashboard.php" class="btn btn-primary btn-full" style="margin-top: 1rem;">Dashboard</a>
                    <button id="logout-button-mobile" class="btn btn-outline btn-full" style="margin-top: 0.5rem;">Sair</button>
                <?php else: ?>
                    <a href="/refactor/pages/auth/login.php" class="btn btn-outline btn-full" style="margin-top: 1rem;">Entrar</a>
                    <a href="/refactor/pages/criar.php" class="btn btn-secondary btn-full" style="margin-top: 0.5rem;">Criar Meu Conto</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <?php
        // Exibe mensagem flash se existir
        $flash = getFlashMessage();
        if ($flash):
        ?>
            <div class="container" style="padding-top: 2rem;">
                <div class="auth-alert auth-alert-<?php echo $flash['type']; ?>" style="animation: fadeIn 0.3s ease-out;">
                    <?php echo e($flash['message']); ?>
                </div>
            </div>
        <?php endif; ?>

        <script>
            // Função para scroll suave em seções
            function scrollToSection(sectionId, event) {
                if (event) event.preventDefault();

                // Se não estiver na home, redireciona com hash
                if (!window.location.pathname.includes('index.php') && window.location.pathname !== '/refactor/' && window.location.pathname !== '/refactor') {
                    window.location.href = '/refactor/index.php#' + sectionId;
                    return;
                }

                const section = document.getElementById(sectionId);
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            // Toggle menu do usuário
            function toggleUserMenu() {
                const menu = document.getElementById('user-menu');
                if (menu) {
                    menu.classList.toggle('hidden');
                }
            }

            // Fecha menu ao clicar fora
            document.addEventListener('click', function(event) {
                const userAvatar = document.querySelector('.user-avatar');
                const userMenu = document.getElementById('user-menu');

                if (userAvatar && userMenu && !userAvatar.contains(event.target) && !userMenu.contains(event.target)) {
                    userMenu.classList.add('hidden');
                }
            });

            // Estilo hover para menu dropdown
            document.addEventListener('DOMContentLoaded', function() {
                const menuLinks = document.querySelectorAll('#user-menu a, #user-menu button');
                menuLinks.forEach(link => {
                    link.addEventListener('mouseenter', function() {
                        this.style.background = 'var(--color-muted)';
                    });
                    link.addEventListener('mouseleave', function() {
                        this.style.background = 'transparent';
                    });
                });
            });
        </script>
