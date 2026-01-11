<!-- Admin Sidebar -->
<aside class="admin-sidebar" style="width: 260px; height: 100vh; position: fixed; left: 0; top: 0; background: var(--color-card); border-right: 1px solid var(--color-border); display: flex; flex-direction: column; z-index: 100;">
    <!-- Logo -->
    <div style="padding: 1.5rem; border-bottom: 1px solid var(--color-border);">
        <a href="<?php echo url('pages/admin/index.php'); ?>" class="logo" style="text-decoration: none;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            <span style="font-weight: 700;">Seu Conto</span>
        </a>
        <p style="font-size: 0.75rem; color: var(--color-muted-foreground); margin-top: 0.25rem; margin-bottom: 0;">Admin Panel</p>
    </div>

    <!-- Navigation -->
    <nav style="flex: 1; padding: 1.5rem 1rem; overflow-y: auto;">
        <ul style="list-style: none; padding: 0; margin: 0;">
            <!-- Dashboard -->
            <li style="margin-bottom: 0.5rem;">
                <a href="<?php echo url('pages/admin/index.php'); ?>" class="sidebar-link <?php echo (basename($_SERVER['PHP_SELF']) === 'index.php' && strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? 'active' : ''; ?>" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: var(--radius); color: var(--color-foreground); text-decoration: none; transition: all var(--transition-fast);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Pedidos -->
            <li style="margin-bottom: 0.5rem;">
                <a href="<?php echo url('pages/admin/orders/index.php'); ?>" class="sidebar-link <?php echo strpos($_SERVER['PHP_SELF'], '/orders/') !== false ? 'active' : ''; ?>" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: var(--radius); color: var(--color-foreground); text-decoration: none; transition: all var(--transition-fast);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    </svg>
                    <span>Pedidos</span>
                </a>
            </li>

            <!-- Leads -->
            <li style="margin-bottom: 0.5rem;">
                <a href="<?php echo url('pages/admin/leads/index.php'); ?>" class="sidebar-link <?php echo strpos($_SERVER['PHP_SELF'], '/leads/') !== false ? 'active' : ''; ?>" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: var(--radius); color: var(--color-foreground); text-decoration: none; transition: all var(--transition-fast);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <line x1="19" y1="8" x2="19" y2="14"></line>
                        <line x1="22" y1="11" x2="16" y2="11"></line>
                    </svg>
                    <span>Leads</span>
                </a>
            </li>

            <!-- Monitor IA -->
            <li style="margin-bottom: 0.5rem;">
                <a href="<?php echo url('pages/admin/ai-monitor/index.php'); ?>" class="sidebar-link <?php echo strpos($_SERVER['PHP_SELF'], '/ai-monitor/') !== false ? 'active' : ''; ?>" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: var(--radius); color: var(--color-foreground); text-decoration: none; transition: all var(--transition-fast);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="7.5 4.21 12 6.81 16.5 4.21"></polyline>
                        <polyline points="7.5 19.79 7.5 14.6 3 12"></polyline>
                        <polyline points="21 12 16.5 14.6 16.5 19.79"></polyline>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    <span>Monitor IA</span>
                </a>
            </li>

            <!-- Configurações -->
            <li style="margin-bottom: 0.5rem;">
                <a href="<?php echo url('pages/admin/settings/index.php'); ?>" class="sidebar-link <?php echo strpos($_SERVER['PHP_SELF'], '/settings/') !== false ? 'active' : ''; ?>" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: var(--radius); color: var(--color-foreground); text-decoration: none; transition: all var(--transition-fast);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M12 1v6m0 6v6m8.66-12.66l-5.83 3.46m-5.66 3.2L3.34 23M23 12h-6m-6 0H5m17.66-4.34l-5.83 3.46m-5.66 3.2L3.34 1"></path>
                    </svg>
                    <span>Configurações</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- User Info Footer -->
    <div style="padding: 1.5rem; border-top: 1px solid var(--color-border);">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 40px; height: 40px; background: var(--color-primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                <?php echo strtoupper(substr($currentUser['name'] ?? 'A', 0, 1)); ?>
            </div>
            <div style="flex: 1; min-width: 0;">
                <p style="font-weight: 600; font-size: 0.875rem; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?php echo e($currentUser['name'] ?? 'Admin'); ?>
                </p>
                <a href="<?php echo url('index.php'); ?>" style="font-size: 0.75rem; color: var(--color-muted-foreground); text-decoration: none;">
                    Ver Site →
                </a>
            </div>
        </div>
    </div>
</aside>

<style>
.sidebar-link:hover {
    background: var(--color-muted);
}

.sidebar-link.active {
    background: var(--color-primary);
    color: white;
}

.sidebar-link.active svg {
    stroke: white;
}
</style>
