-- ============================================
-- SEU CONTO - Usuários de Teste
-- ============================================
-- Execute este SQL no phpMyAdmin
-- ============================================

-- ============================================
-- USUÁRIOS DE TESTE
-- ============================================

-- Usuário Cliente (Teste)
-- Email: cliente@teste.com
-- Senha: teste123
INSERT INTO users (full_name, email, phone, password_hash, role, created_at) VALUES
(
    'Cliente Teste',
    'cliente@teste.com',
    '11999999999',
    '$2y$12$A5rwkZY/ukuVNcjFMMQdE.EvTAVYcdYXz9z9wuMH6B5J4uPtp9zyW', -- senha: teste123
    'user',
    NOW()
);

-- Usuário Admin
-- Email: admin@seuconto.com
-- Senha: admin123
INSERT INTO users (full_name, email, phone, password_hash, role, created_at) VALUES
(
    'Administrador',
    'admin@seuconto.com',
    '11988888888',
    '$2y$12$MzM8tOTwPOJgSU5SgWnGwONTn8dTj4AHac2qbM6FsvsnNAt9nPJtq', -- senha: admin123
    'admin',
    NOW()
);

-- ============================================
-- TABELA DE LIVROS POR USUÁRIO (Reservada)
-- ============================================
-- Estrutura básica - campos de dados do livro serão adicionados depois

CREATE TABLE IF NOT EXISTS user_books (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Relacionamento com usuário
    user_id INT NOT NULL,

    -- Relacionamento com pedido (opcional)
    order_id INT NULL,

    -- Identificador único do livro
    book_uuid VARCHAR(36) NOT NULL UNIQUE,

    -- Status do livro
    status ENUM(
        'generating',   -- Em geração
        'ready',        -- Pronto para visualização
        'downloaded',   -- Já foi baixado
        'archived'      -- Arquivado
    ) DEFAULT 'generating' NOT NULL,

    -- URLs dos arquivos
    pdf_url VARCHAR(500) NULL,
    preview_url VARCHAR(500) NULL,

    -- Contadores
    download_count INT DEFAULT 0,
    view_count INT DEFAULT 0,

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ready_at TIMESTAMP NULL,
    last_downloaded_at TIMESTAMP NULL,

    -- TODO: Adicionar campos de dados do livro aqui:
    -- title VARCHAR(255),
    -- child_name VARCHAR(100),
    -- theme VARCHAR(50),
    -- pages_count INT,
    -- metadata JSON,
    -- etc.

    -- Foreign Keys e Indexes
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_order_id (order_id),
    INDEX idx_book_uuid (book_uuid),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CREDENCIAIS DE ACESSO
-- ============================================
--
-- CLIENTE TESTE:
--   Email: cliente@teste.com
--   Senha: teste123
--
-- ADMIN:
--   Email: admin@seuconto.com
--   Senha: admin123
--
-- ============================================
