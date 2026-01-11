<?php
/**
 * Configurações Gerais da Aplicação
 *
 * Este arquivo contém constantes públicas e configurações globais
 * que não são sensíveis e podem ser versionadas
 */

// ============================================
// INFORMAÇÕES DO SITE
// ============================================

define('SITE_NAME', 'Seu Conto');
define('SITE_TAGLINE', 'Histórias Mágicas Personalizadas');
define('SITE_DESCRIPTION', 'Transforme a imaginação em realidade com e-books personalizados criados por IA');
define('SITE_KEYWORDS', 'livro infantil, e-book personalizado, histórias infantis, IA, personalização');

// ============================================
// CONTATO E SUPORTE
// ============================================

define('SUPPORT_EMAIL', 'contato@seuconto.com.br');
define('SUPPORT_PHONE', '+55 (11) 9999-9999');
define('SOCIAL_INSTAGRAM', 'https://instagram.com/seuconto');
define('SOCIAL_FACEBOOK', 'https://facebook.com/seuconto');

// ============================================
// CONFIGURAÇÕES DE PRODUTO
// ============================================

// Preços (em centavos)
define('PRICE_EBOOK', (int) env('PRICE_EBOOK', 2990));  // R$ 29,90
define('PRICE_COLORING_BOOK', (int) env('PRICE_COLORING_BOOK', 990));  // R$ 9,90

// Limites de caracteres
define('MAX_CHILD_NAME_LENGTH', 50);
define('MAX_CHARACTERISTICS_LENGTH', 500);
define('MAX_DEDICATION_LENGTH', 300);

// Opções de idade
define('MIN_CHILD_AGE', 0);
define('MAX_CHILD_AGE', 12);

// Gêneros disponíveis
define('AVAILABLE_GENDERS', ['masculino', 'feminino', 'outro']);

// Temas disponíveis
define('AVAILABLE_THEMES', [
    'aventura' => [
        'name' => 'Aventura',
        'description' => 'Explorações emocionantes e descobertas',
        'icon' => 'compass',
        'color' => '#FF6B6B'
    ],
    'fantasia' => [
        'name' => 'Fantasia',
        'description' => 'Mundo mágico com criaturas fantásticas',
        'icon' => 'sparkles',
        'color' => '#A78BFA'
    ],
    'espaco' => [
        'name' => 'Espaço',
        'description' => 'Viagem intergaláctica pelo universo',
        'icon' => 'rocket',
        'color' => '#60A5FA'
    ],
    'animais' => [
        'name' => 'Animais',
        'description' => 'Amigos animais e natureza',
        'icon' => 'paw',
        'color' => '#34D399'
    ],
    'princesa' => [
        'name' => 'Princesa/Príncipe',
        'description' => 'Castelos, reinos e realeza',
        'icon' => 'crown',
        'color' => '#F472B6'
    ],
    'super-heroi' => [
        'name' => 'Super-Herói',
        'description' => 'Poderes especiais e missões heroicas',
        'icon' => 'shield',
        'color' => '#FBBF24'
    ]
]);

// ============================================
// CONFIGURAÇÕES DE UPLOAD
// ============================================

define('UPLOAD_MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// Tempo de expiração de arquivos temporários (em segundos)
define('TEMP_FILE_EXPIRATION', 24 * 60 * 60); // 24 horas

// ============================================
// CONFIGURAÇÕES DE GERAÇÃO
// ============================================

// Tempo estimado de geração (em minutos)
define('ESTIMATED_GENERATION_TIME', 5);

// Intervalo de polling para status (em milissegundos)
define('STATUS_POLLING_INTERVAL', 3000); // 3 segundos

// Status possíveis de pedido
define('ORDER_STATUSES', [
    'pending' => 'Aguardando Pagamento',
    'paid' => 'Pagamento Confirmado',
    'generating' => 'Gerando Conteúdo',
    'processing' => 'Processando Livro',
    'completed' => 'Concluído',
    'failed' => 'Falhou',
    'refunded' => 'Reembolsado',
    'cancelled' => 'Cancelado'
]);

// ============================================
// CONFIGURAÇÕES DE SEGURANÇA
// ============================================

// Duração da sessão (em segundos)
define('SESSION_LIFETIME', 7 * 24 * 60 * 60); // 7 dias

// Tentativas de login
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 15 * 60); // 15 minutos

// Hash de senha
define('PASSWORD_MIN_LENGTH', 8);

// ============================================
// CONFIGURAÇÕES DE EMAIL
// ============================================

define('EMAIL_FROM_NAME', SITE_NAME);
define('EMAIL_FROM_ADDRESS', 'noreply@seuconto.com.br');

// ============================================
// TIMEZONE E LOCALIZAÇÃO
// ============================================

define('DEFAULT_TIMEZONE', 'America/Sao_Paulo');
define('DEFAULT_LOCALE', 'pt_BR');
define('DEFAULT_CURRENCY', 'BRL');

// Define o timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// ============================================
// MODO DE DESENVOLVIMENTO
// ============================================

define('IS_DEVELOPMENT', env('APP_ENV', 'production') === 'development');
define('DEBUG_MODE', env('DEBUG', false));

// Configura exibição de erros
if (IS_DEVELOPMENT || DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', 0);
}

// ============================================
// FUNÇÕES AUXILIARES
// ============================================

/**
 * Retorna informações de um tema
 *
 * @param string $themeKey Chave do tema
 * @return array|null
 */
function getThemeInfo($themeKey) {
    $themes = AVAILABLE_THEMES;
    return $themes[$themeKey] ?? null;
}

/**
 * Retorna label de um status de pedido
 *
 * @param string $status Status do pedido
 * @return string
 */
function getOrderStatusLabel($status) {
    $statuses = ORDER_STATUSES;
    return $statuses[$status] ?? 'Desconhecido';
}

/**
 * Formata valor em centavos para Real brasileiro
 *
 * @param int $cents Valor em centavos
 * @return string Valor formatado (ex: "R$ 29,90")
 */
function formatPrice($cents) {
    $reais = $cents / 100;
    return 'R$ ' . number_format($reais, 2, ',', '.');
}

/**
 * Formata data no padrão brasileiro
 *
 * @param string $date Data no formato SQL
 * @param bool $includeTime Se deve incluir hora
 * @return string
 */
function formatDate($date, $includeTime = false) {
    if (empty($date)) return '-';

    $timestamp = strtotime($date);
    $format = $includeTime ? 'd/m/Y H:i' : 'd/m/Y';

    return date($format, $timestamp);
}

/**
 * Verifica se um tema é válido
 *
 * @param string $theme
 * @return bool
 */
function isValidTheme($theme) {
    return array_key_exists($theme, AVAILABLE_THEMES);
}

/**
 * Verifica se um gênero é válido
 *
 * @param string $gender
 * @return bool
 */
function isValidGender($gender) {
    return in_array($gender, AVAILABLE_GENDERS);
}

/**
 * Verifica se uma idade é válida
 *
 * @param int $age
 * @return bool
 */
function isValidAge($age) {
    return is_numeric($age) && $age >= MIN_CHILD_AGE && $age <= MAX_CHILD_AGE;
}
