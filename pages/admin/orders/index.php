<?php
/**
 * Gerenciamento de Pedidos - Listagem
 *
 * Permite visualizar, filtrar e gerenciar todos os pedidos do sistema
 */

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/permissions.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/admin-middleware.php';
require_once __DIR__ . '/../../../services/OrderService.php';

// Proteger rota - apenas admins
protectAdminRoute('admin');

// Obter parâmetros de filtro
$status = $_GET['status'] ?? null;
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;

// Preparar filtros
$filters = [
    'limit' => $perPage,
    'offset' => ($page - 1) * $perPage,
    'order_by' => 'created_at',
    'order_dir' => 'DESC'
];

if ($status && $status !== 'all') {
    $filters['status'] = $status;
}

if (!empty($search)) {
    $filters['search'] = $search;
}

// Buscar pedidos
try {
    $orders = OrderService::getUserOrders(null, $filters);

    // Contar total de pedidos (para paginação)
    $totalFilters = $filters;
    unset($totalFilters['limit']);
    unset($totalFilters['offset']);
    $allOrders = OrderService::getUserOrders(null, $totalFilters);
    $totalOrders = count($allOrders);
    $totalPages = ceil($totalOrders / $perPage);

} catch (Exception $e) {
    error_log("Erro ao buscar pedidos: " . $e->getMessage());
    $orders = [];
    $totalOrders = 0;
    $totalPages = 1;
}

$pageTitle = 'Gerenciar Pedidos';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?> - Seu Conto Admin</title>

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo url('assets/css/main.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('assets/css/admin.css'); ?>">
</head>
<body class="admin-layout">

    <?php include __DIR__ . '/../../../components/admin/sidebar.php'; ?>

    <div class="admin-main">

        <?php include __DIR__ . '/../../../components/admin/topbar.php'; ?>

        <div class="admin-content">

            <!-- Header -->
            <div class="page-header">
                <div>
                    <h1>Pedidos</h1>
                    <p class="text-muted"><?php echo number_format($totalOrders, 0, ',', '.'); ?> pedidos encontrados</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-outline" onclick="exportOrders()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                        </svg>
                        Exportar CSV
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters-bar">
                <form method="GET" class="filters-form">

                    <!-- Search Input -->
                    <div class="filter-group">
                        <input
                            type="text"
                            name="search"
                            placeholder="Buscar por nome, email, ID..."
                            value="<?php echo e($search); ?>"
                            class="filter-input"
                        >
                    </div>

                    <!-- Status Filter -->
                    <div class="filter-group">
                        <select name="status" class="filter-select">
                            <option value="all" <?php echo $status === 'all' || !$status ? 'selected' : ''; ?>>
                                Todos os Status
                            </option>
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>
                                Pendente
                            </option>
                            <option value="processing" <?php echo $status === 'processing' ? 'selected' : ''; ?>>
                                Processando
                            </option>
                            <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>
                                Concluído
                            </option>
                            <option value="failed" <?php echo $status === 'failed' ? 'selected' : ''; ?>>
                                Falhou
                            </option>
                            <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>
                                Cancelado
                            </option>
                        </select>
                    </div>

                    <!-- Submit Buttons -->
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        Filtrar
                    </button>

                    <?php if (!empty($search) || $status): ?>
                        <a href="<?php echo url('pages/admin/orders/index.php'); ?>" class="btn btn-ghost">
                            Limpar Filtros
                        </a>
                    <?php endif; ?>

                </form>
            </div>

            <!-- Orders Table -->
            <div class="table-card">
                <div class="table-card-body">
                    <?php if (empty($orders)): ?>
                        <div class="empty-state">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                            </svg>
                            <h3>Nenhum pedido encontrado</h3>
                            <p>Tente ajustar os filtros ou adicionar novos pedidos</p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Criança</th>
                                    <th>Produto</th>
                                    <th>Tema</th>
                                    <th>Status</th>
                                    <th>Valor</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>
                                            <code>#<?php echo e($order['id']); ?></code>
                                        </td>
                                        <td>
                                            <div class="user-cell">
                                                <div class="user-avatar">
                                                    <?php echo strtoupper(substr($order['user_email'] ?? 'U', 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="user-name"><?php echo e($order['user_name'] ?? 'N/A'); ?></div>
                                                    <div class="user-email"><?php echo e($order['user_email'] ?? ''); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?php echo e($order['child_name'] ?? 'N/A'); ?></strong><br>
                                            <span class="text-muted text-sm">
                                                <?php echo e($order['child_age'] ?? ''); ?> anos
                                            </span>
                                        </td>
                                        <td>
                                            <span class="product-badge product-<?php echo e($order['product_type']); ?>">
                                                <?php echo e($order['product_type'] === 'ebook' ? 'E-book' : 'Livro Físico'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $themes = AVAILABLE_THEMES;
                                            $themeName = $themes[$order['theme']]['name'] ?? ucfirst($order['theme']);
                                            $themeColor = $themes[$order['theme']]['color'] ?? '#999';
                                            ?>
                                            <span class="theme-badge" style="background: <?php echo e($themeColor); ?>20; color: <?php echo e($themeColor); ?>">
                                                <?php echo e($themeName); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo e($order['status']); ?>">
                                                <?php
                                                $statusLabels = [
                                                    'pending' => 'Pendente',
                                                    'processing' => 'Processando',
                                                    'completed' => 'Concluído',
                                                    'failed' => 'Falhou',
                                                    'cancelled' => 'Cancelado'
                                                ];
                                                echo e($statusLabels[$order['status']] ?? $order['status']);
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?php echo formatPrice($order['amount']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                <?php echo date('d/m/Y', strtotime($order['created_at'])); ?><br>
                                                <span class="text-sm"><?php echo date('H:i', strtotime($order['created_at'])); ?></span>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo url('pages/admin/orders/view.php?id=' . $order['id']); ?>"
                                                   class="btn btn-sm btn-primary"
                                                   title="Ver Detalhes">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>
                                                </a>
                                                <?php if ($order['status'] === 'pending' || $order['status'] === 'processing'): ?>
                                                    <button
                                                        class="btn btn-sm btn-danger"
                                                        onclick="cancelOrder(<?php echo $order['id']; ?>)"
                                                        title="Cancelar Pedido">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                                        </svg>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="table-card-footer">
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?><?php echo $status ? '&status=' . $status : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
                                   class="pagination-btn">
                                    Anterior
                                </a>
                            <?php endif; ?>

                            <span class="pagination-info">
                                Página <?php echo $page; ?> de <?php echo $totalPages; ?>
                            </span>

                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?><?php echo $status ? '&status=' . $status : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
                                   class="pagination-btn">
                                    Próxima
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>

    </div>

    <!-- Scripts -->
    <script>
        // Função para exportar pedidos
        function exportOrders() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'csv');
            window.location.href = '<?php echo url('api/admin/export-orders.php'); ?>?' + params.toString();
        }

        // Função para cancelar pedido
        async function cancelOrder(orderId) {
            if (!confirm('Tem certeza que deseja cancelar este pedido?')) {
                return;
            }

            const reason = prompt('Motivo do cancelamento (opcional):');

            try {
                const response = await fetch('<?php echo url('api/admin/cancel-order.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        reason: reason || ''
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Pedido cancelado com sucesso!');
                    window.location.reload();
                } else {
                    alert('Erro ao cancelar pedido: ' + (result.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao cancelar pedido:', error);
                alert('Erro ao cancelar pedido. Tente novamente.');
            }
        }
    </script>

</body>
</html>
