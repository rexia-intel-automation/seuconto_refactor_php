<!-- KPI Card Component -->
<?php
/**
 * Componente de Card KPI para Dashboard Admin
 *
 * Uso:
 * $kpiData = [
 *     'title' => 'Total de Pedidos',
 *     'value' => '150',
 *     'change' => '+12.5%',
 *     'trend' => 'up', // 'up' ou 'down'
 *     'icon' => '<svg>...</svg>'
 * ];
 * include 'components/admin/kpi-card.php';
 */

$title = $kpiData['title'] ?? '';
$value = $kpiData['value'] ?? '0';
$change = $kpiData['change'] ?? null;
$trend = $kpiData['trend'] ?? 'neutral';
$icon = $kpiData['icon'] ?? '';
$bgColor = $kpiData['bgColor'] ?? 'var(--color-primary)';
?>

<div class="kpi-card" style="background: var(--color-card); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 1.5rem; transition: all var(--transition-base);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-lg)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
    <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem;">
        <div style="flex: 1;">
            <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin: 0 0 0.5rem 0; font-weight: 500;">
                <?php echo e($title); ?>
            </p>
            <p style="font-size: 2rem; font-weight: 700; margin: 0; color: var(--color-foreground);">
                <?php echo e($value); ?>
            </p>
        </div>

        <?php if ($icon): ?>
            <div style="width: 48px; height: 48px; background: <?php echo $bgColor; ?>; opacity: 0.1; border-radius: var(--radius); display: flex; align-items: center; justify-content: center;">
                <div style="color: <?php echo $bgColor; ?>;">
                    <?php echo $icon; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($change !== null): ?>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <?php if ($trend === 'up'): ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" stroke-width="2">
                    <polyline points="18 15 12 9 6 15"></polyline>
                </svg>
                <span style="font-size: 0.875rem; color: var(--color-success); font-weight: 600;">
                    <?php echo e($change); ?>
                </span>
            <?php elseif ($trend === 'down'): ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-error)" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
                <span style="font-size: 0.875rem; color: var(--color-error); font-weight: 600;">
                    <?php echo e($change); ?>
                </span>
            <?php else: ?>
                <span style="font-size: 0.875rem; color: var(--color-muted-foreground);">
                    <?php echo e($change); ?>
                </span>
            <?php endif; ?>
            <span style="font-size: 0.75rem; color: var(--color-muted-foreground);">vs. mês anterior</span>
        </div>
    <?php endif; ?>
</div>
