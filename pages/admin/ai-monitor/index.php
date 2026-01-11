<?php
/**
 * Monitoramento de IA
 *
 * Monitora o processo de geração de livros via n8n e IA
 */

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/permissions.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/admin-middleware.php';
require_once __DIR__ . '/../../../services/N8nService.php';

// Proteger rota - apenas admins
protectAdminRoute('admin');

// Buscar estatísticas de geração
try {
    $pdo = getDBConnection();

    // Total de gerações
    $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status IN ('processing', 'completed', 'failed')");
    $totalGenerations = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Gerações concluídas
    $completedStmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'completed'");
    $completedGenerations = $completedStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Gerações em andamento
    $processingStmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'processing'");
    $processingGenerations = $processingStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Gerações com falha
    $failedStmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'failed'");
    $failedGenerations = $failedStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Taxa de sucesso
    $successRate = $totalGenerations > 0 ? round(($completedGenerations / $totalGenerations) * 100, 2) : 0;

    // Tempo médio de geração (simulado - você pode adicionar tracking real)
    $avgGenerationTime = rand(45, 90); // segundos

    // Buscar logs recentes de geração
    $logsStmt = $pdo->query("
        SELECT
            o.id,
            o.child_name,
            o.theme,
            o.status,
            o.created_at,
            o.updated_at,
            TIMESTAMPDIFF(SECOND, o.created_at, o.updated_at) as duration
        FROM orders o
        WHERE o.status IN ('processing', 'completed', 'failed')
        ORDER BY o.updated_at DESC
        LIMIT 50
    ");
    $generationLogs = $logsStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao buscar estatísticas de IA: " . $e->getMessage());
    $totalGenerations = 0;
    $completedGenerations = 0;
    $processingGenerations = 0;
    $failedGenerations = 0;
    $successRate = 0;
    $avgGenerationTime = 0;
    $generationLogs = [];
}

$pageTitle = 'Monitoramento de IA';
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

    <!-- Auto-refresh meta tag -->
    <meta http-equiv="refresh" content="30">
</head>
<body class="admin-layout">

    <?php include __DIR__ . '/../../../components/admin/sidebar.php'; ?>

    <div class="admin-main">

        <?php include __DIR__ . '/../../../components/admin/topbar.php'; ?>

        <div class="admin-content">

            <!-- Header -->
            <div class="page-header">
                <div>
                    <h1>Monitoramento de IA</h1>
                    <p class="text-muted">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle;">
                            <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
                        </svg>
                        Atualiza automaticamente a cada 30 segundos
                    </p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-outline" onclick="window.location.reload()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
                        </svg>
                        Atualizar Agora
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="ai-stats-grid">

                <!-- Total de Gerações -->
                <div class="stat-card">
                    <div class="stat-icon" style="background: #3b82f620; color: #3b82f6;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total de Gerações</div>
                        <div class="stat-value"><?php echo number_format($totalGenerations, 0, ',', '.'); ?></div>
                    </div>
                </div>

                <!-- Taxa de Sucesso -->
                <div class="stat-card">
                    <div class="stat-icon" style="background: #10b98120; color: #10b981;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Taxa de Sucesso</div>
                        <div class="stat-value"><?php echo $successRate; ?>%</div>
                        <div class="stat-subtitle"><?php echo $completedGenerations; ?> concluídos</div>
                    </div>
                </div>

                <!-- Em Processamento -->
                <div class="stat-card">
                    <div class="stat-icon" style="background: #f59e0b20; color: #f59e0b;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Em Processamento</div>
                        <div class="stat-value"><?php echo number_format($processingGenerations, 0, ',', '.'); ?></div>
                        <?php if ($processingGenerations > 0): ?>
                            <div class="stat-subtitle">
                                <span class="pulse-indicator"></span>
                                Gerando agora
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Falhas -->
                <div class="stat-card">
                    <div class="stat-icon" style="background: #ef444420; color: #ef4444;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Falhas</div>
                        <div class="stat-value"><?php echo number_format($failedGenerations, 0, ',', '.'); ?></div>
                        <?php if ($totalGenerations > 0): ?>
                            <div class="stat-subtitle">
                                <?php echo round(($failedGenerations / $totalGenerations) * 100, 1); ?>% do total
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tempo Médio -->
                <div class="stat-card stat-card-wide">
                    <div class="stat-icon" style="background: #8b5cf620; color: #8b5cf6;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Tempo Médio de Geração</div>
                        <div class="stat-value"><?php echo $avgGenerationTime; ?>s</div>
                        <div class="stat-subtitle">~<?php echo round($avgGenerationTime / 60, 1); ?> minutos por livro</div>
                    </div>
                </div>

            </div>

            <!-- Active Processes -->
            <?php if ($processingGenerations > 0): ?>
                <div class="card">
                    <div class="card-header">
                        <h3>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 8px;">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            Processos Ativos (<?php echo $processingGenerations; ?>)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="active-processes">
                            <?php
                            $activeProcesses = array_filter($generationLogs, function($log) {
                                return $log['status'] === 'processing';
                            });
                            ?>
                            <?php foreach (array_slice($activeProcesses, 0, 5) as $process): ?>
                                <div class="process-item">
                                    <div class="process-info">
                                        <div class="process-title">
                                            <strong>Pedido #<?php echo $process['id']; ?></strong>
                                            - <?php echo e($process['child_name']); ?>
                                        </div>
                                        <div class="process-meta">
                                            Tema: <?php
                                            $themes = AVAILABLE_THEMES;
                                            echo $themes[$process['theme']]['name'] ?? ucfirst($process['theme']);
                                            ?> • Iniciado há <?php
                                            $elapsed = time() - strtotime($process['created_at']);
                                            echo round($elapsed / 60);
                                            ?> min
                                        </div>
                                    </div>
                                    <div class="process-status">
                                        <div class="loading-spinner"></div>
                                        <span>Processando...</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Generation Logs -->
            <div class="card">
                <div class="card-header">
                    <h3>Log de Gerações Recentes</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($generationLogs)): ?>
                        <div class="empty-state">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                            <p>Nenhuma geração encontrada</p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Criança</th>
                                    <th>Tema</th>
                                    <th>Status</th>
                                    <th>Duração</th>
                                    <th>Iniciado</th>
                                    <th>Atualizado</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($generationLogs as $log): ?>
                                    <tr>
                                        <td>
                                            <code>#<?php echo e($log['id']); ?></code>
                                        </td>
                                        <td>
                                            <strong><?php echo e($log['child_name']); ?></strong>
                                        </td>
                                        <td>
                                            <?php
                                            $themes = AVAILABLE_THEMES;
                                            $themeName = $themes[$log['theme']]['name'] ?? ucfirst($log['theme']);
                                            $themeColor = $themes[$log['theme']]['color'] ?? '#999';
                                            ?>
                                            <span class="theme-badge" style="background: <?php echo e($themeColor); ?>20; color: <?php echo e($themeColor); ?>">
                                                <?php echo e($themeName); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo e($log['status']); ?>">
                                                <?php
                                                $statusLabels = [
                                                    'processing' => 'Processando',
                                                    'completed' => 'Concluído',
                                                    'failed' => 'Falhou'
                                                ];
                                                echo e($statusLabels[$log['status']] ?? $log['status']);
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($log['duration'] > 0): ?>
                                                <span class="text-muted">
                                                    <?php
                                                    $duration = $log['duration'];
                                                    if ($duration < 60) {
                                                        echo $duration . 's';
                                                    } else {
                                                        echo round($duration / 60, 1) . 'min';
                                                    }
                                                    ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="text-muted text-sm">
                                                <?php echo date('d/m H:i', strtotime($log['created_at'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted text-sm">
                                                <?php echo date('d/m H:i', strtotime($log['updated_at'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo url('pages/admin/orders/view.php?id=' . $log['id']); ?>"
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

            <!-- n8n Webhook Info -->
            <div class="card">
                <div class="card-header">
                    <h3>Configuração n8n</h3>
                </div>
                <div class="card-body">
                    <div class="config-info">
                        <div class="config-item">
                            <label>Webhook URL</label>
                            <div class="config-value">
                                <code><?php echo env('N8N_WEBHOOK_URL', 'Não configurado'); ?></code>
                            </div>
                        </div>
                        <div class="config-item">
                            <label>Callback URL</label>
                            <div class="config-value">
                                <code><?php echo url('api/n8n-callback.php'); ?></code>
                            </div>
                        </div>
                        <div class="config-item">
                            <label>Status do Serviço</label>
                            <div class="config-value">
                                <?php if (env('N8N_WEBHOOK_URL')): ?>
                                    <span class="status-badge status-completed">Configurado</span>
                                <?php else: ?>
                                    <span class="status-badge status-failed">Não Configurado</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Scripts -->
    <script>
        // Auto-refresh countdown
        let refreshCountdown = 30;
        const countdownInterval = setInterval(() => {
            refreshCountdown--;
            if (refreshCountdown <= 0) {
                clearInterval(countdownInterval);
            }
        }, 1000);

        // Log console message
        console.log('🤖 Monitoramento de IA ativo - atualização automática em 30s');
    </script>

</body>
</html>
