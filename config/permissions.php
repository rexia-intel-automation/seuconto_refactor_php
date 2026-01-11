<?php
/**
 * Sistema de Permissões (ACL - Access Control List)
 *
 * Define roles (funções) de usuários e suas permissões
 */

// ============================================
// DEFINIÇÃO DE ROLES
// ============================================

define('ROLE_GUEST', 'guest');           // Visitante não autenticado
define('ROLE_USER', 'user');             // Usuário comum (cliente)
define('ROLE_ADMIN', 'admin');           // Administrador
define('ROLE_SUPER_ADMIN', 'super_admin'); // Super Administrador

// ============================================
// DEFINIÇÃO DE PERMISSÕES
// ============================================

/**
 * Mapa de permissões por role
 */
define('PERMISSIONS_MAP', [
    ROLE_GUEST => [
        // Visitantes podem apenas ver conteúdo público
        'view_landing_page',
        'view_legal_pages',
        'create_account',
        'login'
    ],

    ROLE_USER => [
        // Usuários autenticados herdam permissões de guest
        'view_landing_page',
        'view_legal_pages',
        'login',

        // Permissões específicas de usuário
        'create_book',
        'view_own_orders',
        'download_own_books',
        'update_profile',
        'request_free_sample',
        'make_payment'
    ],

    ROLE_ADMIN => [
        // Admin herda todas as permissões de usuário
        'view_landing_page',
        'view_legal_pages',
        'login',
        'create_book',
        'view_own_orders',
        'download_own_books',
        'update_profile',

        // Permissões administrativas
        'access_admin_panel',
        'view_all_orders',
        'view_order_details',
        'update_order_status',
        'view_analytics',
        'view_leads',
        'export_leads',
        'view_ai_monitor',
        'update_settings',
        'manage_prices',
        'manage_prompts',
        'view_logs'
    ],

    ROLE_SUPER_ADMIN => [
        // Super admin tem todas as permissões
        '*' // Wildcard para todas as permissões
    ]
]);

// ============================================
// ROTAS PROTEGIDAS POR ROLE
// ============================================

/**
 * Define quais rotas requerem quais roles mínimos
 */
define('PROTECTED_ROUTES', [
    // Área do usuário
    '/pages/dashboard.php' => ROLE_USER,
    '/pages/create/' => ROLE_USER,
    '/api/trigger-generation.php' => ROLE_USER,
    '/api/check-status.php' => ROLE_USER,

    // Área administrativa
    '/pages/admin/' => ROLE_ADMIN,
    '/api/admin/' => ROLE_ADMIN,

    // Rotas públicas (não listadas aqui são públicas por padrão)
]);

// ============================================
// FUNÇÕES DE VERIFICAÇÃO DE PERMISSÕES
// ============================================

/**
 * Verifica se um usuário tem uma permissão específica
 *
 * @param string $permission Nome da permissão
 * @param array|null $user Dados do usuário (null = usar sessão atual)
 * @return bool
 */
function hasPermission($permission, $user = null) {
    if ($user === null) {
        // Tenta obter usuário da sessão
        if (function_exists('getCurrentUser') && function_exists('isLoggedIn')) {
            if (!isLoggedIn()) {
                $role = ROLE_GUEST;
            } else {
                $user = getCurrentUser();
                $role = $user['role'] ?? ROLE_USER;
            }
        } else {
            $role = ROLE_GUEST;
        }
    } else {
        $role = $user['role'] ?? ROLE_USER;
    }

    $permissions = PERMISSIONS_MAP[$role] ?? [];

    // Super admin tem acesso a tudo
    if (in_array('*', $permissions)) {
        return true;
    }

    return in_array($permission, $permissions);
}

/**
 * Verifica se um usuário tem um role específico
 *
 * @param string $requiredRole Role necessário
 * @param array|null $user Dados do usuário (null = usar sessão atual)
 * @return bool
 */
function hasRole($requiredRole, $user = null) {
    if ($user === null) {
        if (function_exists('getCurrentUser') && function_exists('isLoggedIn')) {
            if (!isLoggedIn()) {
                return $requiredRole === ROLE_GUEST;
            }
            $user = getCurrentUser();
        } else {
            return $requiredRole === ROLE_GUEST;
        }
    }

    $userRole = $user['role'] ?? ROLE_USER;

    // Hierarquia de roles
    $roleHierarchy = [
        ROLE_GUEST => 0,
        ROLE_USER => 1,
        ROLE_ADMIN => 2,
        ROLE_SUPER_ADMIN => 3
    ];

    $userLevel = $roleHierarchy[$userRole] ?? 0;
    $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;

    return $userLevel >= $requiredLevel;
}

/**
 * Verifica se o usuário atual é admin
 *
 * @return bool
 */
function isAdmin() {
    return hasRole(ROLE_ADMIN);
}

/**
 * Verifica se o usuário atual é super admin
 *
 * @return bool
 */
function isSuperAdmin() {
    return hasRole(ROLE_SUPER_ADMIN);
}

/**
 * Requer uma permissão específica ou redireciona
 *
 * @param string $permission Permissão necessária
 * @param string $redirectTo URL de redirecionamento (padrão: login)
 * @throws Exception Se não tiver a permissão
 */
function requirePermission($permission, $redirectTo = null) {
    if (!hasPermission($permission)) {
        if ($redirectTo) {
            if (function_exists('redirectTo')) {
                redirectTo($redirectTo);
            } else {
                header('Location: ' . $redirectTo);
                exit;
            }
        } else {
            http_response_code(403);
            throw new Exception('Você não tem permissão para acessar este recurso');
        }
    }
}

/**
 * Requer um role mínimo ou redireciona
 *
 * @param string $requiredRole Role necessário
 * @param string $redirectTo URL de redirecionamento (padrão: login)
 */
function requireRole($requiredRole, $redirectTo = '/pages/auth/login.php') {
    if (!hasRole($requiredRole)) {
        if (function_exists('redirectTo')) {
            redirectTo($redirectTo);
        } else {
            header('Location: ' . $redirectTo);
            exit;
        }
    }
}

/**
 * Middleware para proteger rotas administrativas
 * Deve ser chamado no início de páginas /admin
 */
function requireAdmin() {
    requireRole(ROLE_ADMIN, '/pages/admin/login.php');
}

/**
 * Verifica se uma rota é protegida e requer autenticação
 *
 * @param string $route Caminho da rota
 * @return string|null Role necessário ou null se pública
 */
function getRouteRequiredRole($route) {
    // Normaliza a rota
    $route = str_replace('\\', '/', $route);

    // Verifica correspondência exata
    if (isset(PROTECTED_ROUTES[$route])) {
        return PROTECTED_ROUTES[$route];
    }

    // Verifica correspondência por prefixo (para diretórios)
    foreach (PROTECTED_ROUTES as $protectedRoute => $role) {
        if (strpos($route, $protectedRoute) === 0) {
            return $role;
        }
    }

    return null; // Rota pública
}

/**
 * Aplica proteção de rota automaticamente
 * Pode ser chamado em um arquivo de bootstrap
 *
 * @param string $currentRoute Rota atual
 */
function applyRouteProtection($currentRoute) {
    $requiredRole = getRouteRequiredRole($currentRoute);

    if ($requiredRole !== null) {
        $redirectMap = [
            ROLE_USER => '/pages/auth/login.php',
            ROLE_ADMIN => '/pages/admin/login.php',
            ROLE_SUPER_ADMIN => '/pages/admin/login.php'
        ];

        $redirectTo = $redirectMap[$requiredRole] ?? '/pages/auth/login.php';
        requireRole($requiredRole, $redirectTo);
    }
}
