-- ============================================
-- SEU CONTO - Estrutura do Banco de Dados
-- ============================================
-- Database: u922209553_primary
-- User: u922209553_seu_conto
-- ============================================

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(320) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Pedidos
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Relacionamento com usuário
    user_id INT,

    -- Informações do cliente
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(320) NOT NULL,
    customer_phone VARCHAR(20),

    -- Informações da criança
    child_name VARCHAR(100) NOT NULL,
    child_age INT NOT NULL,
    child_gender ENUM('menino', 'menina', 'outro') NOT NULL,
    child_characteristics TEXT,
    child_photo_url VARCHAR(500),

    -- Personalização do livro
    theme VARCHAR(50) NOT NULL,
    dedication TEXT,

    -- Produtos incluídos
    includes_coloring_book BOOLEAN DEFAULT FALSE,

    -- Preços (em centavos)
    base_price INT NOT NULL DEFAULT 2990,
    coloring_book_price INT DEFAULT 0,
    total_price INT NOT NULL,

    -- Integração Stripe
    stripe_checkout_session_id VARCHAR(255),
    stripe_payment_intent_id VARCHAR(255),

    -- Status do pedido
    status ENUM(
        'pending',      -- Aguardando pagamento
        'paid',         -- Pago
        'processing',   -- Gerando livro
        'completed',    -- Entregue
        'cancelled',    -- Cancelado
        'refunded'      -- Reembolsado
    ) DEFAULT 'pending' NOT NULL,

    -- Método de entrega
    delivery_method ENUM('email', 'whatsapp', 'both') DEFAULT 'email',
    delivered_at TIMESTAMP NULL,

    -- Arquivos gerados
    book_file_url VARCHAR(500),
    coloring_book_file_url VARCHAR(500),

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    paid_at TIMESTAMP NULL,

    -- Foreign Keys e Indexes
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_customer_email (customer_email),
    INDEX idx_status (status),
    INDEX idx_stripe_session (stripe_checkout_session_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Sessões (para gerenciamento de login)
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Temas (dados estáticos dos temas disponíveis)
CREATE TABLE IF NOT EXISTS themes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    emoji VARCHAR(10),
    description TEXT,
    color_primary VARCHAR(50),
    color_secondary VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir temas padrão
INSERT INTO themes (slug, name, emoji, description, color_primary, color_secondary, display_order) VALUES
('coragem', 'Coragem', '🐉', 'Histórias de bravura e superação de medos', 'oklch(0.65 0.20 240)', 'oklch(0.75 0.15 240)', 1),
('amizade', 'Amizade', '🤝', 'Aventuras sobre companheirismo e lealdade', 'oklch(0.75 0.18 340)', 'oklch(0.85 0.15 340)', 2),
('exploracao', 'Exploração', '🦖', 'Descobertas e aventuras pelo desconhecido', 'oklch(0.70 0.18 145)', 'oklch(0.80 0.15 145)', 3),
('magia', 'Magia', '🧚', 'Mundos encantados cheios de mistério', 'oklch(0.70 0.15 280)', 'oklch(0.80 0.12 280)', 4)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description);

-- Limpar sessões expiradas (executar periodicamente via cron)
-- DELETE FROM sessions WHERE expires_at < NOW();

-- ============================================
-- Views úteis para consultas
-- ============================================

-- View de pedidos com informações do usuário
CREATE OR REPLACE VIEW orders_with_user AS
SELECT
    o.*,
    u.full_name as user_full_name,
    u.email as user_email,
    t.name as theme_name,
    t.emoji as theme_emoji
FROM orders o
LEFT JOIN users u ON o.user_id = u.id
LEFT JOIN themes t ON o.theme = t.slug;

-- View de estatísticas de pedidos
CREATE OR REPLACE VIEW order_stats AS
SELECT
    DATE(created_at) as date,
    COUNT(*) as total_orders,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
    SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_orders,
    SUM(CASE WHEN includes_coloring_book THEN 1 ELSE 0 END) as with_coloring,
    SUM(total_price) / 100 as total_revenue
FROM orders
GROUP BY DATE(created_at)
ORDER BY date DESC;
