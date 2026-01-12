<?php
/**
 * Dashboard do Usuário - Meus Livros
 *
 * Área privada onde o usuário visualiza seus pedidos
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../services/OrderService.php';

// Proteger rota - apenas usuários logados
requireAuth();
$user = getCurrentUser();

// Buscar pedidos do usuário usando OrderService
try {
    $orders = OrderService::getUserOrders($user['id']);
} catch (Exception $e) {
    error_log("Erro ao buscar pedidos do usuário: " . $e->getMessage());
    $orders = [];
}

// Calcular estatísticas
$totalOrders = count($orders);
$completedOrders = count(array_filter($orders, fn($o) => $o['status'] === 'completed'));
$processingOrders = count(array_filter($orders, fn($o) => $o['status'] === 'processing'));

$pageTitle = 'Meus Livros - Seu Conto';
$pageDescription = 'Gerencie seus livros personalizados criados com IA';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php include __DIR__ . '/../components/head.php'; ?>
    <link rel="stylesheet" href="<?php echo url('assets/css/dashboard.css'); ?>">
</head>
<body>

    <?php include __DIR__ . '/../components/header.php'; ?>

    <main class="dashboard-container">
        <div class="container">

            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="dashboard-welcome">
                    <h1>Olá, <?php echo e(explode(' ', $user['name'])[0]); ?>! 👋</h1>
                    <p class="text-muted">Bem-vindo ao seu painel de livros mágicos</p>
                </div>
                <a href="<?php echo url('pages/create/step1-theme.php'); ?>" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Criar Novo Livro
                </a>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $totalOrders; ?></h3>
                        <p>Livros Criados</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon success">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $completedOrders; ?></h3>
                        <p>Concluídos</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $processingOrders; ?></h3>
                        <p>Em Processamento</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon secondary">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo e($user['name']); ?></h3>
                        <p>Minha Conta</p>
                    </div>
                </div>
            </div>

            <!-- Books Section -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                        Meus Livros
                    </h2>
                </div>

                <?php if (empty($orders)): ?>
                    <!-- Empty State -->
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                <line x1="10" y1="8" x2="16" y2="8"/>
                                <line x1="10" y1="12" x2="16" y2="12"/>
                                <line x1="10" y1="16" x2="13" y2="16"/>
                            </svg>
                        </div>
                        <h3>Nenhum livro criado ainda</h3>
                        <p>Comece agora a criar histórias mágicas personalizadas!</p>
                        <a href="<?php echo url('pages/create/step1-theme.php'); ?>" class="btn btn-primary btn-lg" style="margin-top: 1.5rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.5rem;">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Criar Meu Primeiro Livro
                        </a>
                    </div>

                <?php else: ?>
                    <!-- Books Grid -->
                    <div class="books-grid">
                        <?php foreach ($orders as $order): ?>
                            <?php
                            // Determinar cor e label do status
                            $statusLabels = [
                                'pending' => ['label' => 'Pendente', 'class' => 'pending'],
                                'processing' => ['label' => 'Processando', 'class' => 'processing'],
                                'completed' => ['label' => 'Pronto', 'class' => 'completed'],
                                'failed' => ['label' => 'Falhou', 'class' => 'failed'],
                                'cancelled' => ['label' => 'Cancelado', 'class' => 'cancelled']
                            ];
                            $statusInfo = $statusLabels[$order['status']] ?? ['label' => $order['status'], 'class' => 'pending'];

                            // Determinar cor do tema
                            $themes = AVAILABLE_THEMES;
                            $themeInfo = $themes[$order['theme']] ?? ['name' => ucfirst($order['theme']), 'color' => '#999'];
                            ?>

                            <div class="book-card">
                                <div class="book-cover" style="background: linear-gradient(135deg, <?php echo $themeInfo['color']; ?>30, <?php echo $themeInfo['color']; ?>10);">
                                    <div class="book-cover-placeholder">
                                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="<?php echo $themeInfo['color']; ?>" stroke-width="2">
                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                        </svg>
                                        <p class="text-muted" style="margin-top: 0.5rem; font-size: 0.875rem;"><?php echo e($themeInfo['name']); ?></p>
                                    </div>
                                    <span class="book-status-badge <?php echo $statusInfo['class']; ?>">
                                        <?php echo e($statusInfo['label']); ?>
                                    </span>
                                </div>

                                <div class="book-info">
                                    <span class="book-theme" style="background: <?php echo $themeInfo['color']; ?>20; color: <?php echo $themeInfo['color']; ?>;">
                                        <?php echo e($themeInfo['name']); ?>
                                    </span>
                                    <h3 class="book-title">História de <?php echo e($order['child_name']); ?></h3>

                                    <div class="book-meta">
                                        <span>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            <?php echo date('d/m/Y', strtotime($order['created_at'])); ?>
                                        </span>
                                        <span>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                                <circle cx="12" cy="7" r="4"/>
                                            </svg>
                                            <?php echo e($order['child_age']); ?> anos
                                        </span>
                                    </div>

                                    <div class="book-actions">
                                        <?php if ($order['status'] === 'completed' && !empty($order['book_file_url'])): ?>
                                            <a href="<?php echo e($order['book_file_url']); ?>" class="btn btn-primary" target="_blank" download>
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                    <polyline points="7 10 12 15 17 10"/>
                                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                                </svg>
                                                Baixar PDF
                                            </a>
                                        <?php elseif ($order['status'] === 'processing'): ?>
                                            <button class="btn btn-outline" disabled>
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 2s linear infinite;">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <polyline points="12 6 12 12 16 14"/>
                                                </svg>
                                                Em Produção
                                            </button>
                                        <?php elseif ($order['status'] === 'failed'): ?>
                                            <button class="btn btn-outline btn-danger" disabled>
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <line x1="15" y1="9" x2="9" y2="15"/>
                                                    <line x1="9" y1="9" x2="15" y2="15"/>
                                                </svg>
                                                Falhou
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-outline" disabled>
                                                Aguardando...
                                            </button>
                                        <?php endif; ?>

                                        <button class="btn btn-ghost btn-sm" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            Detalhes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Scripts -->
    <script>
        function viewOrderDetails(orderId) {
            // Modal ou redirecionamento para página de detalhes
            alert('Visualizar detalhes do pedido #' + orderId);
            // TODO: Implementar modal ou página de detalhes
        }

        // Auto-refresh para pedidos em processamento
        <?php if ($processingOrders > 0): ?>
        setTimeout(() => {
            console.log('Atualizando status dos pedidos...');
            window.location.reload();
        }, 30000); // 30 segundos
        <?php endif; ?>
    </script>

    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

</body>
</html>
