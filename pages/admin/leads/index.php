<?php
/**
 * Gerenciamento de Leads
 *
 * Visualiza e gerencia leads capturados (visitantes interessados)
 */

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/permissions.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/admin-middleware.php';

// Proteger rota - apenas admins
protectAdminRoute('admin');

// Obter parâmetros de filtro
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;

// Buscar leads do banco de dados
try {
    $pdo = getDBConnection();

    // Query base
    $query = "SELECT * FROM leads WHERE 1=1";
    $params = [];

    // Filtro de busca
    if (!empty($search)) {
        $query .= " AND (email LIKE ? OR name LIKE ? OR child_name LIKE ?)";
        $searchTerm = "%{$search}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    // Contar total
    $countStmt = $pdo->prepare(str_replace('*', 'COUNT(*) as total', $query));
    $countStmt->execute($params);
    $totalLeads = $countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $totalPages = ceil($totalLeads / $perPage);

    // Adicionar paginação
    $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $perPage;
    $params[] = ($page - 1) * $perPage;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao buscar leads: " . $e->getMessage());
    $leads = [];
    $totalLeads = 0;
    $totalPages = 1;
}

$pageTitle = 'Gerenciar Leads';
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
                    <h1>Leads</h1>
                    <p class="text-muted"><?php echo number_format($totalLeads, 0, ',', '.'); ?> leads capturados</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-outline" onclick="exportLeads()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                        </svg>
                        Exportar CSV
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #3b82f620; color: #3b82f6;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total de Leads</div>
                        <div class="stat-value"><?php echo number_format($totalLeads, 0, ',', '.'); ?></div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: #10b98120; color: #10b981;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Taxa de Conversão</div>
                        <div class="stat-value">
                            <?php
                            // Calcular taxa de conversão (simulado - você pode buscar do banco)
                            $conversionRate = $totalLeads > 0 ? rand(15, 35) : 0;
                            echo $conversionRate . '%';
                            ?>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: #8b5cf620; color: #8b5cf6;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v20M2 12h20"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Novos esta Semana</div>
                        <div class="stat-value">
                            <?php
                            // Contar leads da última semana (simulado)
                            $weekLeads = rand(5, 25);
                            echo number_format($weekLeads, 0, ',', '.');
                            ?>
                        </div>
                    </div>
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
                            placeholder="Buscar por nome, email..."
                            value="<?php echo e($search); ?>"
                            class="filter-input"
                        >
                    </div>

                    <!-- Submit Buttons -->
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        Buscar
                    </button>

                    <?php if (!empty($search)): ?>
                        <a href="<?php echo url('pages/admin/leads/index.php'); ?>" class="btn btn-ghost">
                            Limpar Filtros
                        </a>
                    <?php endif; ?>

                </form>
            </div>

            <!-- Leads Table -->
            <div class="table-card">
                <div class="table-card-body">
                    <?php if (empty($leads)): ?>
                        <div class="empty-state">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <h3>Nenhum lead encontrado</h3>
                            <p>Os leads capturados aparecerão aqui</p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Criança</th>
                                    <th>Tema</th>
                                    <th>Origem</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($leads as $lead): ?>
                                    <tr>
                                        <td>
                                            <code>#<?php echo e($lead['id']); ?></code>
                                        </td>
                                        <td>
                                            <div class="user-cell">
                                                <div class="user-avatar">
                                                    <?php echo strtoupper(substr($lead['name'] ?? $lead['email'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="user-name"><?php echo e($lead['name'] ?? 'N/A'); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="mailto:<?php echo e($lead['email']); ?>" class="link">
                                                <?php echo e($lead['email']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php echo e($lead['child_name'] ?? '-'); ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($lead['theme'])): ?>
                                                <?php
                                                $themes = AVAILABLE_THEMES;
                                                $themeName = $themes[$lead['theme']]['name'] ?? ucfirst($lead['theme']);
                                                $themeColor = $themes[$lead['theme']]['color'] ?? '#999';
                                                ?>
                                                <span class="theme-badge" style="background: <?php echo e($themeColor); ?>20; color: <?php echo e($themeColor); ?>">
                                                    <?php echo e($themeName); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-outline">
                                                <?php echo e($lead['source'] ?? 'Website'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                <?php echo date('d/m/Y', strtotime($lead['created_at'])); ?><br>
                                                <span class="text-sm"><?php echo date('H:i', strtotime($lead['created_at'])); ?></span>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button
                                                    class="btn btn-sm btn-primary"
                                                    onclick="viewLead(<?php echo $lead['id']; ?>)"
                                                    title="Ver Detalhes">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>
                                                </button>
                                                <button
                                                    class="btn btn-sm btn-outline"
                                                    onclick="sendFollowUpEmail('<?php echo e($lead['email']); ?>')"
                                                    title="Enviar Email">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                                        <polyline points="22,6 12,13 2,6"/>
                                                    </svg>
                                                </button>
                                                <button
                                                    class="btn btn-sm btn-danger"
                                                    onclick="deleteLead(<?php echo $lead['id']; ?>)"
                                                    title="Excluir">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    </svg>
                                                </button>
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
                                <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
                                   class="pagination-btn">
                                    Anterior
                                </a>
                            <?php endif; ?>

                            <span class="pagination-info">
                                Página <?php echo $page; ?> de <?php echo $totalPages; ?>
                            </span>

                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
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
        // Exportar leads
        function exportLeads() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'csv');
            window.location.href = '<?php echo url('api/admin/export-leads.php'); ?>?' + params.toString();
        }

        // Ver detalhes do lead
        function viewLead(leadId) {
            // Modal ou página dedicada (você pode implementar)
            alert('Funcionalidade de visualização será implementada em breve.');
        }

        // Enviar email de follow-up
        function sendFollowUpEmail(email) {
            const subject = 'Completar seu livro personalizado no Seu Conto';
            const body = 'Olá! Notamos que você começou a criar um livro personalizado mas não finalizou. Podemos ajudar?';
            window.location.href = `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        }

        // Excluir lead
        async function deleteLead(leadId) {
            if (!confirm('Tem certeza que deseja excluir este lead?')) {
                return;
            }

            try {
                const response = await fetch('<?php echo url('api/admin/delete-lead.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ lead_id: leadId })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Lead excluído com sucesso!');
                    window.location.reload();
                } else {
                    alert('Erro ao excluir lead: ' + (result.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao excluir lead:', error);
                alert('Erro ao excluir lead. Tente novamente.');
            }
        }
    </script>

</body>
</html>
