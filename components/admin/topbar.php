<!-- Admin Topbar -->
<div class="admin-topbar" style="height: 70px; background: var(--color-card); border-bottom: 1px solid var(--color-border); display: flex; align-items: center; padding: 0 2rem; position: sticky; top: 0; z-index: 50;">
    <div style="flex: 1; display: flex; align-items: center; gap: 1.5rem;">
        <!-- Page Title -->
        <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0; color: var(--color-foreground);">
            <?php echo $pageTitle ?? 'Dashboard'; ?>
        </h1>

        <?php if (isset($pageSubtitle)): ?>
            <span style="color: var(--color-muted-foreground); font-size: 0.875rem;">
                <?php echo $pageSubtitle; ?>
            </span>
        <?php endif; ?>
    </div>

    <!-- Right Section -->
    <div style="display: flex; align-items: center; gap: 1rem;">
        <!-- Notifications -->
        <button style="width: 40px; height: 40px; border-radius: 50%; background: var(--color-muted); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; position: relative; transition: all var(--transition-fast);" onmouseover="this.style.background='var(--color-border)'" onmouseout="this.style.background='var(--color-muted)'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <!-- Badge de notificação -->
            <span style="position: absolute; top: 5px; right: 5px; width: 8px; height: 8px; background: var(--color-error); border-radius: 50%; border: 2px solid var(--color-card);"></span>
        </button>

        <!-- User Menu -->
        <div style="position: relative;">
            <button onclick="toggleAdminUserMenu()" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 1rem; background: var(--color-muted); border: none; border-radius: var(--radius-lg); cursor: pointer; transition: all var(--transition-fast);" onmouseover="this.style.background='var(--color-border)'" onmouseout="this.style.background='var(--color-muted)'">
                <div style="width: 32px; height: 32px; background: var(--color-primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem;">
                    <?php echo strtoupper(substr($currentUser['name'] ?? 'A', 0, 1)); ?>
                </div>
                <span style="font-weight: 600; font-size: 0.875rem;">
                    <?php echo e(explode(' ', $currentUser['name'] ?? 'Admin')[0]); ?>
                </span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>

            <!-- Dropdown -->
            <div id="admin-user-menu" class="hidden" style="position: absolute; right: 0; top: calc(100% + 0.5rem); background: var(--color-card); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); min-width: 200px; z-index: 1000;">
                <div style="padding: 0.5rem;">
                    <a href="<?php echo url('index.php'); ?>" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: var(--radius); color: var(--color-foreground); text-decoration: none; transition: background var(--transition-fast);" onmouseover="this.style.background='var(--color-muted)'" onmouseout="this.style.background='transparent'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        Ver Site
                    </a>
                    <button id="admin-logout-button" style="width: 100%; display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: var(--radius); color: var(--color-error); background: transparent; border: none; cursor: pointer; transition: background var(--transition-fast); font-family: var(--font-body); text-align: left;" onmouseover="this.style.background='var(--color-muted)'" onmouseout="this.style.background='transparent'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Sair
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAdminUserMenu() {
    const menu = document.getElementById('admin-user-menu');
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

// Fecha menu ao clicar fora
document.addEventListener('click', function(event) {
    const button = event.target.closest('[onclick="toggleAdminUserMenu()"]');
    const menu = document.getElementById('admin-user-menu');

    if (!button && menu && !menu.contains(event.target)) {
        menu.classList.add('hidden');
    }
});
</script>
