<?php
$pageTitle = 'Meus Livros - Seu Conto';
$additionalCSS = ['/refactor/assets/css/dashboard.css'];
$additionalJS = [];

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';

requireAuth();
$user = getCurrentUser();

// Busca livros do usuário
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? OR customer_email = ? ORDER BY created_at DESC");
    $stmt->execute([$user['id'], $user['email']]);
    $orders = $stmt->fetchAll();
} catch (Exception $e) {
    $orders = [];
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-container">
    <div class="container">
        <div class="dashboard-header">
            <div class="dashboard-welcome">
                <div>
                    <h1>Olá, <?php echo e(explode(' ', $user['name'])[0]); ?>! 👋</h1>
                    <p class="text-muted">Bem-vindo ao seu painel de livros mágicos</p>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo count($orders); ?></h3>
                    <p>Livros Criados</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo count(array_filter($orders, fn($o) => $o['status'] === 'completed')); ?></h3>
                    <p>Concluídos</p>
                </div>
            </div>
        </div>

        <div class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    Meus Livros
                </h2>
                <a href="/refactor/pages/criar.php" class="btn btn-primary">+ Criar Novo Livro</a>
            </div>

            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    </div>
                    <h3>Nenhum livro criado ainda</h3>
                    <p>Comece agora a criar histórias mágicas para suas crianças!</p>
                    <a href="/refactor/pages/criar.php" class="btn btn-primary btn-lg">Criar Meu Primeiro Livro</a>
                </div>
            <?php else: ?>
                <div class="books-grid">
                    <?php foreach ($orders as $order): ?>
                        <div class="book-card">
                            <div class="book-cover">
                                <div class="book-cover-placeholder">
                                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                    <p class="text-muted"><?php echo ucfirst($order['theme']); ?></p>
                                </div>
                                <span class="book-status-badge <?php echo $order['status']; ?>">
                                    <?php echo $order['status'] === 'completed' ? 'Pronto' : 'Processando'; ?>
                                </span>
                            </div>
                            <div class="book-info">
                                <span class="book-theme"><?php echo ucfirst($order['theme']); ?></span>
                                <h3 class="book-title">História de <?php echo e($order['child_name']); ?></h3>
                                <div class="book-meta">
                                    <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg><?php echo formatDate($order['created_at']); ?></span>
                                </div>
                                <div class="book-actions">
                                    <?php if ($order['status'] === 'completed' && $order['book_file_url']): ?>
                                        <a href="<?php echo e($order['book_file_url']); ?>" class="btn btn-primary" target="_blank">Baixar PDF</a>
                                    <?php else: ?>
                                        <button class="btn btn-outline" disabled>Em Produção</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
