<?php
/**
 * Gerenciamento de Pedidos - Visualização Detalhada
 *
 * Exibe todos os detalhes de um pedido específico
 */

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/permissions.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/admin-middleware.php';
require_once __DIR__ . '/../../../services/OrderService.php';
require_once __DIR__ . '/../../../services/N8nService.php';

// Proteger rota - apenas admins
protectAdminRoute('admin');

// Obter ID do pedido
$orderId = intval($_GET['id'] ?? 0);

if (!$orderId) {
    header('Location: ' . url('pages/admin/orders/index.php'));
    exit;
}

// Buscar dados do pedido
try {
    $order = OrderService::getOrder($orderId);

    if (!$order) {
        throw new Exception('Pedido não encontrado');
    }

    // Buscar histórico de status (simulado - você pode adicionar uma tabela de histórico)
    $statusHistory = [
        [
            'status' => 'pending',
            'timestamp' => $order['created_at'],
            'description' => 'Pedido criado'
        ]
    ];

    if ($order['updated_at'] !== $order['created_at']) {
        $statusHistory[] = [
            'status' => $order['status'],
            'timestamp' => $order['updated_at'],
            'description' => 'Status atualizado para ' . $order['status']
        ];
    }

} catch (Exception $e) {
    error_log("Erro ao buscar pedido: " . $e->getMessage());
    header('Location: ' . url('pages/admin/orders/index.php?error=not_found'));
    exit;
}

$pageTitle = 'Pedido #' . $order['id'];

// Labels de status
$statusLabels = [
    'pending' => 'Pendente',
    'processing' => 'Processando',
    'completed' => 'Concluído',
    'failed' => 'Falhou',
    'cancelled' => 'Cancelado'
];

// Temas
$themes = AVAILABLE_THEMES;
$themeName = $themes[$order['theme']]['name'] ?? ucfirst($order['theme']);
$themeColor = $themes[$order['theme']]['color'] ?? '#999';
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
                    <div class="breadcrumb">
                        <a href="<?php echo url('pages/admin/orders/index.php'); ?>">Pedidos</a>
                        <span>/</span>
                        <span>#<?php echo e($order['id']); ?></span>
                    </div>
                    <h1>Pedido #<?php echo e($order['id']); ?></h1>
                    <p class="text-muted">
                        Criado em <?php echo date('d/m/Y \à\s H:i', strtotime($order['created_at'])); ?>
                    </p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-outline" onclick="window.print()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                            <rect x="6" y="14" width="12" height="8"/>
                        </svg>
                        Imprimir
                    </button>
                    <?php if ($order['status'] === 'pending' || $order['status'] === 'processing'): ?>
                        <button class="btn btn-danger" onclick="cancelOrder(<?php echo $order['id']; ?>)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                            Cancelar Pedido
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="order-view-grid">

                <!-- Coluna Principal -->
                <div class="order-main-column">

                    <!-- Status Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Status do Pedido</h3>
                        </div>
                        <div class="card-body">
                            <div class="status-display">
                                <span class="status-badge status-<?php echo e($order['status']); ?> status-large">
                                    <?php echo e($statusLabels[$order['status']] ?? $order['status']); ?>
                                </span>
                                <?php if ($order['status'] === 'processing'): ?>
                                    <button class="btn btn-sm btn-primary" onclick="checkGenerationStatus()">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
                                        </svg>
                                        Verificar Status
                                    </button>
                                <?php endif; ?>
                            </div>

                            <?php if ($order['book_file_url']): ?>
                                <div class="book-download-section">
                                    <h4>Livro Gerado</h4>
                                    <a href="<?php echo e($order['book_file_url']); ?>"
                                       class="btn btn-success btn-block"
                                       download>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                                        </svg>
                                        Baixar Livro (PDF)
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Order Details Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Detalhes do Pedido</h3>
                        </div>
                        <div class="card-body">
                            <div class="detail-grid">

                                <div class="detail-item">
                                    <label>Nome da Criança</label>
                                    <div class="detail-value">
                                        <?php echo e($order['child_name']); ?>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <label>Idade</label>
                                    <div class="detail-value">
                                        <?php echo e($order['child_age']); ?> anos
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <label>Tema</label>
                                    <div class="detail-value">
                                        <span class="theme-badge" style="background: <?php echo e($themeColor); ?>20; color: <?php echo e($themeColor); ?>">
                                            <?php echo e($themeName); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <label>Tipo de Produto</label>
                                    <div class="detail-value">
                                        <span class="product-badge product-<?php echo e($order['product_type']); ?>">
                                            <?php echo e($order['product_type'] === 'ebook' ? 'E-book' : 'Livro Físico'); ?>
                                        </span>
                                    </div>
                                </div>

                                <?php if ($order['child_photo_url']): ?>
                                    <div class="detail-item detail-full">
                                        <label>Foto da Criança</label>
                                        <div class="detail-value">
                                            <img src="<?php echo e($order['child_photo_url']); ?>"
                                                 alt="Foto de <?php echo e($order['child_name']); ?>"
                                                 class="child-photo-preview">
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>

                    <!-- Status History -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Histórico de Status</h3>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <?php foreach (array_reverse($statusHistory) as $history): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <div class="timeline-status">
                                                <span class="status-badge status-<?php echo e($history['status']); ?>">
                                                    <?php echo e($statusLabels[$history['status']] ?? $history['status']); ?>
                                                </span>
                                            </div>
                                            <div class="timeline-description">
                                                <?php echo e($history['description']); ?>
                                            </div>
                                            <div class="timeline-time">
                                                <?php echo date('d/m/Y H:i', strtotime($history['timestamp'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Coluna Lateral -->
                <div class="order-sidebar-column">

                    <!-- Customer Info Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Informações do Cliente</h3>
                        </div>
                        <div class="card-body">
                            <div class="customer-info">
                                <div class="customer-avatar">
                                    <?php echo strtoupper(substr($order['user_email'] ?? 'U', 0, 1)); ?>
                                </div>
                                <div class="customer-details">
                                    <div class="customer-name">
                                        <?php echo e($order['user_name'] ?? 'N/A'); ?>
                                    </div>
                                    <div class="customer-email">
                                        <a href="mailto:<?php echo e($order['user_email']); ?>">
                                            <?php echo e($order['user_email']); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Informações de Pagamento</h3>
                        </div>
                        <div class="card-body">
                            <div class="payment-info">

                                <div class="payment-row">
                                    <span class="payment-label">Subtotal</span>
                                    <span class="payment-value"><?php echo formatPrice($order['amount']); ?></span>
                                </div>

                                <div class="payment-row payment-total">
                                    <span class="payment-label">Total</span>
                                    <span class="payment-value"><?php echo formatPrice($order['amount']); ?></span>
                                </div>

                                <?php if ($order['stripe_payment_intent_id']): ?>
                                    <div class="payment-details">
                                        <small class="text-muted">
                                            <strong>Payment Intent ID:</strong><br>
                                            <code><?php echo e($order['stripe_payment_intent_id']); ?></code>
                                        </small>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>

                    <!-- Metadata Card -->
                    <?php if (!empty($order['metadata'])): ?>
                        <div class="card">
                            <div class="card-header">
                                <h3>Metadados</h3>
                            </div>
                            <div class="card-body">
                                <pre class="metadata-display"><?php echo e(json_encode(json_decode($order['metadata']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Actions Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Ações</h3>
                        </div>
                        <div class="card-body">
                            <div class="actions-list">
                                <?php if ($order['status'] === 'failed'): ?>
                                    <button class="btn btn-primary btn-block" onclick="retryGeneration()">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
                                        </svg>
                                        Tentar Novamente
                                    </button>
                                <?php endif; ?>

                                <button class="btn btn-outline btn-block" onclick="sendEmailToCustomer()">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                        <polyline points="22,6 12,13 2,6"/>
                                    </svg>
                                    Enviar Email
                                </button>

                                <a href="<?php echo url('pages/admin/orders/index.php'); ?>"
                                   class="btn btn-ghost btn-block">
                                    Voltar para Listagem
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Scripts -->
    <script>
        const orderId = <?php echo $order['id']; ?>;

        // Verificar status de geração
        async function checkGenerationStatus() {
            try {
                const response = await fetch(`<?php echo url('api/check-order-status.php'); ?>?order_id=${orderId}`);
                const result = await response.json();

                if (result.success) {
                    alert('Status atualizado! A página será recarregada.');
                    window.location.reload();
                } else {
                    alert('Status: ' + result.status);
                }
            } catch (error) {
                console.error('Erro ao verificar status:', error);
                alert('Erro ao verificar status. Tente novamente.');
            }
        }

        // Cancelar pedido
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

        // Tentar geração novamente
        async function retryGeneration() {
            if (!confirm('Deseja tentar gerar o livro novamente?')) {
                return;
            }

            try {
                const response = await fetch('<?php echo url('api/admin/retry-generation.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ order_id: orderId })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Geração reiniciada com sucesso!');
                    window.location.reload();
                } else {
                    alert('Erro ao reiniciar geração: ' + (result.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao reiniciar geração:', error);
                alert('Erro ao reiniciar geração. Tente novamente.');
            }
        }

        // Enviar email ao cliente
        function sendEmailToCustomer() {
            const email = '<?php echo e($order['user_email']); ?>';
            const subject = 'Sobre seu pedido #<?php echo $order['id']; ?>';
            window.location.href = `mailto:${email}?subject=${encodeURIComponent(subject)}`;
        }
    </script>

</body>
</html>
