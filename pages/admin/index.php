<?php
/**
 * Dashboard Administrativo - Página Principal
 *
 * Exibe métricas, KPIs e gráficos para monitoramento do negócio
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/permissions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/admin-middleware.php';
require_once __DIR__ . '/../../services/AnalyticsService.php';
require_once __DIR__ . '/../../services/OrderService.php';

// Proteger rota - apenas admins
protectAdminRoute('admin');

// Obter dados do dashboard
try {
    // KPIs principais
    $kpis = AnalyticsService::getMainKPIs('month');

    // Dados para gráficos
    $ordersChartData = AnalyticsService::getOrdersChart(30); // últimos 30 dias
    $popularThemes = AnalyticsService::getPopularThemes(5);

    // Taxas de crescimento
    $ordersGrowth = AnalyticsService::getGrowthRate('orders', 'month');
    $revenueGrowth = AnalyticsService::getGrowthRate('revenue', 'month');
    $usersGrowth = AnalyticsService::getGrowthRate('users', 'month');

    // Pedidos recentes
    $recentOrders = OrderService::getUserOrders(null, [
        'limit' => 10,
        'order_by' => 'created_at',
        'order_dir' => 'DESC'
    ]);

} catch (Exception $e) {
    error_log("Erro ao carregar dashboard: " . $e->getMessage());
    $kpis = [
        'total_orders' => 0,
        'total_revenue' => 0,
        'total_users' => 0,
        'avg_order_value' => 0
    ];
    $ordersChartData = [];
    $popularThemes = [];
    $ordersGrowth = 0;
    $revenueGrowth = 0;
    $usersGrowth = 0;
    $recentOrders = [];
}

$pageTitle = 'Dashboard Administrativo';
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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="admin-layout">

    <?php include __DIR__ . '/../../components/admin/sidebar.php'; ?>

    <div class="admin-main">

        <?php include __DIR__ . '/../../components/admin/topbar.php'; ?>

        <div class="admin-content">

            <!-- Header -->
            <div class="page-header">
                <h1>Dashboard</h1>
                <div class="page-actions">
                    <button class="btn btn-outline" onclick="window.location.reload()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
                        </svg>
                        Atualizar
                    </button>
                    <button class="btn btn-primary" onclick="exportDashboardData()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                        </svg>
                        Exportar
                    </button>
                </div>
            </div>

            <!-- KPIs Grid -->
            <div class="kpi-grid">

                <!-- Total de Pedidos -->
                <?php
                include __DIR__ . '/../../components/admin/kpi-card.php';
                renderKPICard([
                    'title' => 'Total de Pedidos',
                    'value' => number_format($kpis['total_orders'], 0, ',', '.'),
                    'icon' => 'shopping-bag',
                    'color' => 'blue',
                    'trend' => $ordersGrowth,
                    'trendLabel' => 'vs. mês anterior'
                ]);
                ?>

                <!-- Receita Total -->
                <?php
                renderKPICard([
                    'title' => 'Receita Total',
                    'value' => formatPrice($kpis['total_revenue']),
                    'icon' => 'dollar-sign',
                    'color' => 'green',
                    'trend' => $revenueGrowth,
                    'trendLabel' => 'vs. mês anterior'
                ]);
                ?>

                <!-- Total de Usuários -->
                <?php
                renderKPICard([
                    'title' => 'Usuários Cadastrados',
                    'value' => number_format($kpis['total_users'], 0, ',', '.'),
                    'icon' => 'users',
                    'color' => 'purple',
                    'trend' => $usersGrowth,
                    'trendLabel' => 'vs. mês anterior'
                ]);
                ?>

                <!-- Ticket Médio -->
                <?php
                renderKPICard([
                    'title' => 'Ticket Médio',
                    'value' => formatPrice($kpis['avg_order_value']),
                    'icon' => 'trending-up',
                    'color' => 'orange',
                    'trend' => 0,
                    'trendLabel' => 'últimos 30 dias'
                ]);
                ?>

            </div>

            <!-- Charts Row -->
            <div class="charts-row">

                <!-- Pedidos nos últimos 30 dias -->
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h3>Pedidos nos Últimos 30 Dias</h3>
                        <select id="ordersChartPeriod" onchange="updateOrdersChart(this.value)">
                            <option value="7">7 dias</option>
                            <option value="30" selected>30 dias</option>
                            <option value="90">90 dias</option>
                        </select>
                    </div>
                    <div class="chart-card-body">
                        <canvas id="ordersChart"></canvas>
                    </div>
                </div>

                <!-- Temas Mais Populares -->
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h3>Temas Mais Populares</h3>
                    </div>
                    <div class="chart-card-body">
                        <canvas id="themesChart"></canvas>
                    </div>
                </div>

            </div>

            <!-- Recent Orders Table -->
            <div class="table-card">
                <div class="table-card-header">
                    <h3>Pedidos Recentes</h3>
                    <a href="<?php echo url('pages/admin/orders/index.php'); ?>" class="btn btn-sm btn-outline">
                        Ver Todos
                    </a>
                </div>
                <div class="table-card-body">
                    <?php if (empty($recentOrders)): ?>
                        <div class="empty-state">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                            </svg>
                            <p>Nenhum pedido encontrado</p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Produto</th>
                                    <th>Status</th>
                                    <th>Valor</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td>
                                            <code>#<?php echo e($order['id']); ?></code>
                                        </td>
                                        <td>
                                            <div class="user-cell">
                                                <div class="user-avatar">
                                                    <?php echo strtoupper(substr($order['child_name'] ?? 'U', 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="user-name"><?php echo e($order['child_name'] ?? 'N/A'); ?></div>
                                                    <div class="user-email"><?php echo e($order['user_email'] ?? ''); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="product-badge">
                                                <?php echo e($order['product_type'] === 'ebook' ? 'E-book' : 'Livro Físico'); ?>
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
                                                <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo url('pages/admin/orders/view.php?id=' . $order['id']); ?>"
                                               class="btn btn-sm btn-ghost">
                                                Ver Detalhes
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>

    <!-- Scripts -->
    <script src="<?php echo url('assets/js/admin-charts.js'); ?>"></script>
    <script>
        // Dados para os gráficos
        const ordersChartData = <?php echo json_encode($ordersChartData); ?>;
        const themesChartData = <?php echo json_encode($popularThemes); ?>;

        // Inicializar gráficos quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            initOrdersChart(ordersChartData);
            initThemesChart(themesChartData);
        });

        // Função para exportar dados do dashboard
        function exportDashboardData() {
            window.location.href = '<?php echo url('api/admin/export-dashboard.php'); ?>';
        }

        // Função para atualizar período do gráfico de pedidos
        async function updateOrdersChart(days) {
            try {
                const response = await fetch(`<?php echo url('api/admin/get-orders-chart.php'); ?>?days=${days}`);
                const data = await response.json();

                if (data.success) {
                    initOrdersChart(data.chartData);
                }
            } catch (error) {
                console.error('Erro ao atualizar gráfico:', error);
            }
        }
    </script>

</body>
</html>
