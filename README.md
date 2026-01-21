## URL de testes: https://lightgreen-chamois-286828.hostingersite.com/

# 📚 Seu Conto - Refactor PHP

> Versão refatorada da aplicação de livros infantis personalizados em PHP puro, HTML, CSS e JavaScript

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Stripe](https://img.shields.io/badge/Stripe-API-008CDD?logo=stripe&logoColor=white)](https://stripe.com/)
[![License](https://img.shields.io/badge/License-Proprietary-red)]()

## 📋 Índice

- [Visão Geral](#-visão-geral)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Requisitos](#-requisitos)
- [Instalação](#-instalação)
- [Configuração](#-configuração)
- [Banco de Dados](#-banco-de-dados)
- [Arquitetura](#-arquitetura)
- [Páginas](#-páginas)
- [APIs](#-apis)
- [Segurança](#-segurança)
- [Deploy](#-deploy)
- [Troubleshooting](#-troubleshooting)

---

## 🎯 Visão Geral

Esta é uma versão refatorada em **PHP puro** da aplicação Seu Conto, originalmente desenvolvida em React/Node.js. O objetivo é criar uma versão simplificada para testes em ambiente Hostinger, mantendo todas as funcionalidades principais:

### Funcionalidades

✨ **Principais Features:**
- 🎨 Livros infantis personalizados com IA
- 👤 Sistema completo de autenticação (login/registro)
- 📖 Dashboard do usuário com gestão de livros
- 🛒 Checkout integrado com Stripe
- 📧 Múltiplas formas de entrega (Email/WhatsApp)
- 🎨 4 temas disponíveis: Coragem, Amizade, Exploração, Magia
- 🖼️ Opção de livro de colorir adicional

### Tecnologias Utilizadas

- **Backend:** PHP 8.0+, MySQL 8.0+
- **Frontend:** HTML5, CSS3 (OKLch), JavaScript ES6+
- **Pagamentos:** Stripe API
- **Database:** phpMyAdmin (Hostinger)
- **Previsão:** Supabase (armazenamento futuro)

---

## 📁 Estrutura do Projeto

```
/refactor/
├── config/
│   ├── .env                    # Variáveis de ambiente
│   ├── env.php                 # Carregador de .env
│   ├── database.php            # Conexão com BD
│   ├── database.sql            # Schema SQL
│   └── stripe.php              # Configuração Stripe
│
├── assets/
│   ├── css/
│   │   ├── main.css            # Estilos globais
│   │   ├── auth.css            # Estilos de autenticação
│   │   └── dashboard.css       # Estilos do dashboard
│   ├── js/
│   │   ├── main.js             # Funções globais
│   │   ├── auth.js             # Lógica de autenticação
│   │   └── checkout.js         # Lógica de checkout
│   └── images/                 # Imagens e assets
│
├── includes/
│   ├── session.php             # Gerenciamento de sessão
│   ├── functions.php           # Funções utilitárias
│   ├── header.php              # Header global
│   └── footer.php              # Footer global
│
├── pages/
│   ├── auth/
│   │   ├── login.php           # Página de login
│   │   └── register.php        # Página de registro
│   ├── dashboard.php           # Dashboard do usuário
│   ├── criar.php               # Formulário de criação
│   └── checkout.php            # Página de checkout
│
├── api/
│   ├── auth.php                # API de autenticação
│   └── checkout.php            # API de checkout
│
├── index.php                   # Landing page
└── README.md                   # Este arquivo

```

---

## ⚙️ Requisitos

### Servidor

- **PHP:** 8.0 ou superior
- **MySQL:** 8.0 ou superior
- **Apache/Nginx:** com mod_rewrite habilitado
- **Extensões PHP:**
  - PDO e PDO_MySQL
  - OpenSSL
  - mbstring
  - JSON
  - cURL

### Contas Externas

- ✅ Conta Stripe (teste ou produção)
- ⏳ Conta Supabase (opcional, futuro)

---

## 🚀 Instalação

### 1. Clone ou Copie o Diretório

```bash
# Se estiver usando Git
git clone <repository-url> /seu-diretorio/refactor

# Ou copie manualmente os arquivos
cp -r /caminho/seu-conto/refactor /destino/
```

### 2. Configure Permissões

```bash
# Garanta permissões corretas
chmod 755 /refactor
chmod 644 /refactor/config/.env
chmod 755 /refactor/assets
chmod 755 /refactor/pages
chmod 755 /refactor/api
```

### 3. Configure o Banco de Dados

Acesse o phpMyAdmin da Hostinger e:

1. Crie/acesse o banco: `u922209553_primary`
2. Execute o arquivo `config/database.sql`
3. Verifique se todas as tabelas foram criadas

---

## 🔧 Configuração

### Arquivo .env

Edite o arquivo `config/.env` com suas credenciais:

```env
# ============================================
# BANCO DE DADOS
# ============================================
DB_HOST=localhost
DB_NAME=u922209553_primary
DB_USER=u922209553_seu_conto
DB_PASSWORD=sua_senha_aqui
DB_CHARSET=utf8mb4

# ============================================
# STRIPE
# ============================================
STRIPE_SECRET_KEY=sk_test_seu_secret_key_aqui
STRIPE_PUBLISHABLE_KEY=pk_test_seu_publishable_key_aqui
STRIPE_WEBHOOK_SECRET=whsec_seu_webhook_secret_aqui

# Produtos (opcional - criar no Stripe Dashboard)
STRIPE_EBOOK_PRICE_ID=price_ebook_id
STRIPE_COLORING_BOOK_PRICE_ID=price_coloring_id

# ============================================
# SUPABASE (Futuro)
# ============================================
SUPABASE_URL=https://seu-projeto.supabase.co
SUPABASE_ANON_KEY=sua_anon_key_aqui
SUPABASE_SERVICE_KEY=sua_service_key_aqui
SUPABASE_BUCKET_NAME=seu-conto-books

# ============================================
# APLICAÇÃO
# ============================================
APP_NAME="Seu Conto"
APP_URL=https://seu-dominio.com
APP_ENV=production
APP_DEBUG=false

SESSION_SECRET=gere_uma_string_aleatoria_de_32_caracteres
SESSION_LIFETIME=86400

# ============================================
# PREÇOS (em centavos)
# ============================================
PRICE_EBOOK=2990
PRICE_COLORING_BOOK=990
```

### Stripe - Configuração

1. Acesse [Stripe Dashboard](https://dashboard.stripe.com/)
2. Em **Developers > API Keys**, copie suas chaves
3. Configure webhook endpoint: `https://seu-dominio.com/refactor/api/webhook.php`
4. Selecione eventos: `checkout.session.completed`, `payment_intent.succeeded`
5. Copie o webhook secret

---

## 💾 Banco de Dados

### Schema Completo

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│   users     │       │   orders    │       │   themes    │
├─────────────┤       ├─────────────┤       ├─────────────┤
│ id (PK)     │◄──────┤ user_id(FK) │       │ id (PK)     │
│ full_name   │       │ theme ──────┼──────►│ slug        │
│ email       │       │ status      │       │ name        │
│ phone       │       │ ...         │       │ emoji       │
│ password    │       └─────────────┘       │ ...         │
│ role        │              │              └─────────────┘
│ ...         │              │
└─────────────┘              │
      │                      │
      │    ┌─────────────────┘
      │    │
      ▼    ▼
┌─────────────┐       ┌─────────────┐
│ user_books  │       │  sessions   │
├─────────────┤       ├─────────────┤
│ id (PK)     │       │ id (PK)     │
│ user_id(FK) │       │ user_id(FK) │
│ order_id(FK)│       │ expires_at  │
│ book_uuid   │       │ ...         │
│ status      │       └─────────────┘
│ pdf_url     │
│ ...         │
└─────────────┘
```

### Tabelas

#### `users`
Armazena dados dos usuários registrados.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT AUTO_INCREMENT | ID único (PK) |
| full_name | VARCHAR(255) | Nome completo |
| email | VARCHAR(320) UNIQUE | Email (login) |
| phone | VARCHAR(20) | Telefone/WhatsApp |
| password_hash | VARCHAR(255) | Senha criptografada (bcrypt) |
| role | ENUM('user','admin') | Papel do usuário |
| created_at | TIMESTAMP | Data de criação |
| updated_at | TIMESTAMP | Última atualização |
| last_login | TIMESTAMP | Último login |

#### `orders`
Armazena pedidos de livros.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT AUTO_INCREMENT | ID único (PK) |
| user_id | INT | FK para users (opcional) |
| customer_name | VARCHAR(255) | Nome do cliente |
| customer_email | VARCHAR(320) | Email |
| customer_phone | VARCHAR(20) | Telefone |
| child_name | VARCHAR(100) | Nome da criança |
| child_age | INT | Idade (0-12) |
| child_gender | ENUM | 'menino', 'menina', 'outro' |
| child_characteristics | TEXT | Descrição física |
| child_photo_url | VARCHAR(500) | URL da foto |
| theme | VARCHAR(50) | Tema escolhido (FK para themes.slug) |
| dedication | TEXT | Dedicatória personalizada |
| includes_coloring_book | BOOLEAN | Livro de colorir incluso |
| base_price | INT | Preço base (centavos) |
| coloring_book_price | INT | Preço do colorir (centavos) |
| total_price | INT | Preço total (centavos) |
| stripe_checkout_session_id | VARCHAR(255) | Session Stripe |
| stripe_payment_intent_id | VARCHAR(255) | Payment Intent |
| status | ENUM | 'pending', 'paid', 'processing', 'completed', 'cancelled', 'refunded' |
| delivery_method | ENUM | 'email', 'whatsapp', 'both' |
| delivered_at | TIMESTAMP | Data de entrega |
| book_file_url | VARCHAR(500) | URL do PDF |
| coloring_book_file_url | VARCHAR(500) | URL do colorir |
| created_at | TIMESTAMP | Data de criação |
| updated_at | TIMESTAMP | Última atualização |
| paid_at | TIMESTAMP | Data do pagamento |

#### `user_books`
Biblioteca de livros do usuário (estrutura reservada).

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT AUTO_INCREMENT | ID único (PK) |
| user_id | INT | FK para users (obrigatório) |
| order_id | INT | FK para orders (opcional) |
| book_uuid | VARCHAR(36) UNIQUE | UUID do livro |
| status | ENUM | 'generating', 'ready', 'downloaded', 'archived' |
| pdf_url | VARCHAR(500) | URL do PDF |
| preview_url | VARCHAR(500) | URL do preview |
| download_count | INT | Total de downloads |
| view_count | INT | Total de visualizações |
| created_at | TIMESTAMP | Data de criação |
| updated_at | TIMESTAMP | Última atualização |
| ready_at | TIMESTAMP | Data que ficou pronto |
| last_downloaded_at | TIMESTAMP | Último download |

> **Nota:** Campos adicionais de dados do livro (título, páginas, metadata) serão adicionados posteriormente.

#### `sessions`
Gerencia sessões de usuários.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | VARCHAR(128) | ID da sessão (PK) |
| user_id | INT | FK para users |
| expires_at | TIMESTAMP | Expiração |
| created_at | TIMESTAMP | Data de criação |

#### `themes`
Dados estáticos dos temas disponíveis.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT AUTO_INCREMENT | ID único (PK) |
| slug | VARCHAR(50) UNIQUE | Identificador único |
| name | VARCHAR(100) | Nome do tema |
| emoji | VARCHAR(10) | Emoji representativo |
| description | TEXT | Descrição |
| color_primary | VARCHAR(50) | Cor primária (OKLch) |
| color_secondary | VARCHAR(50) | Cor secundária (OKLch) |
| is_active | BOOLEAN | Tema ativo |
| display_order | INT | Ordem de exibição |

**Temas pré-populados:**
| Slug | Nome | Emoji |
|------|------|-------|
| coragem | Coragem | 🐉 |
| amizade | Amizade | 🤝 |
| exploracao | Exploração | 🦖 |
| magia | Magia | 🧚 |

### Views

#### `orders_with_user`
Pedidos com informações do usuário e tema.

#### `order_stats`
Estatísticas diárias de pedidos.

### Usuários de Teste

Para ambiente de desenvolvimento, execute `config/test-users.sql`:

| Tipo | Email | Senha |
|------|-------|-------|
| Cliente | cliente@teste.com | teste123 |
| Admin | admin@seuconto.com | admin123 |

### Migrations

```sql
-- Schema principal
SOURCE config/database.sql;

-- Usuários de teste (opcional)
SOURCE config/test-users.sql;
```

---

## 🏗️ Arquitetura

### Design Patterns Utilizados

#### Singleton (Database)
```php
$db = Database::getInstance()->getConnection();
```

#### Helper Functions
```php
// Validações
isValidEmail($email);
isValidPhone($phone);

// Formatações
formatPrice(2990); // R$ 29,90
formatDate($date); // 10/01/2026

// Segurança
e($string); // XSS protection
hashPassword($password);
```

#### Session Management
```php
requireAuth(); // Protege rotas
isLoggedIn(); // Verifica login
getCurrentUser(); // Dados do usuário
```

### Fluxo de Dados

```
┌─────────────┐
│   Cliente   │
└──────┬──────┘
       │
       ▼
┌─────────────┐     ┌──────────────┐
│  Frontend   │────▶│ API Endpoint │
│  (HTML/JS)  │     │   (PHP)      │
└─────────────┘     └──────┬───────┘
                           │
                           ▼
                    ┌──────────────┐
                    │   Database   │
                    │   (MySQL)    │
                    └──────────────┘
```

---

## 📄 Páginas

### Landing Page (`index.php`)

**URL:** `/refactor/index.php`

**Seções:**
- Hero com CTA principal
- Como Funciona (3 passos)
- Temas Disponíveis (4 cards)
- Depoimentos
- FAQ
- CTA Final

### Autenticação

**Login:** `/refactor/pages/auth/login.php`
- Email e senha
- Validação em tempo real
- Redirect para dashboard

**Registro:** `/refactor/pages/auth/register.php`
- Nome completo, email, WhatsApp
- Força da senha
- Termos de uso

### Dashboard (`pages/dashboard.php`)

**URL:** `/refactor/pages/dashboard.php`

**Requer:** Autenticação

**Features:**
- Visualização de livros criados
- Estatísticas (total, concluídos)
- Download de PDFs
- CTA criar novo livro

### Criar Livro (`pages/criar.php`)

**URL:** `/refactor/pages/criar.php`

**Fluxo (3 passos):**
1. **Dados da Criança:** Nome, idade, gênero, características
2. **Tema:** Seleção visual + dedicatória
3. **Seus Dados:** Email, WhatsApp, método de entrega

**Armazenamento:** LocalStorage (`storyFormData`)

### Checkout (`pages/checkout.php`)

**URL:** `/refactor/pages/checkout.php`

**Features:**
- Resumo do pedido
- Opção de adicionar livro de colorir
- Cálculo automático de preços
- Integração com Stripe

---

## 🔌 APIs

### Authentication API (`api/auth.php`)

#### POST /refactor/api/auth.php

**Registro:**
```json
{
  "action": "register",
  "fullName": "João Silva",
  "email": "joao@email.com",
  "phone": "(11) 99999-9999",
  "password": "senha123"
}
```

**Login:**
```json
{
  "action": "login",
  "email": "joao@email.com",
  "password": "senha123"
}
```

**Logout:**
```json
{
  "action": "logout"
}
```

### Checkout API (`api/checkout.php`)

#### POST /refactor/api/checkout.php

**Criar Sessão:**
```json
{
  "action": "createCheckoutSession",
  "customerName": "João Silva",
  "customerEmail": "joao@email.com",
  "customerPhone": "(11) 99999-9999",
  "childName": "Maria",
  "childAge": 7,
  "childGender": "menina",
  "childCharacteristics": "Cabelo loiro, ama unicórnios",
  "theme": "magia",
  "dedication": "Para minha filha amada",
  "includesColoringBook": true,
  "deliveryMethod": "both"
}
```

**Resposta:**
```json
{
  "success": true,
  "data": {
    "orderId": 123,
    "checkoutUrl": "https://checkout.stripe.com/...",
    "sessionId": "cs_test_..."
  }
}
```

---

## 🔒 Segurança

### Implementações de Segurança

✅ **XSS Protection:**
```php
// Sempre use a função e() para output
echo e($user['name']);
```

✅ **SQL Injection:**
```php
// Sempre use prepared statements
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

✅ **Password Hashing:**
```php
$hash = hashPassword($password); // bcrypt
$valid = verifyPassword($password, $hash);
```

✅ **CSRF Protection:**
```php
$token = generateCsrfToken();
validateCsrfToken($token);
```

✅ **Session Security:**
- HttpOnly cookies
- Secure flag em produção
- Session regeneration após login

### Headers de Segurança (Recomendado)

Adicione ao `.htaccess`:

```apache
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
```

---

## 🚀 Deploy

### Hostinger - Passo a Passo

1. **Upload de Arquivos:**
   ```
   - Acesse File Manager no hPanel
   - Navegue até public_html
   - Crie diretório: /refactor
   - Faça upload de todos os arquivos
   ```

2. **Configure o Banco:**
   ```
   - Acesse phpMyAdmin
   - Selecione: u922209553_primary
   - Importe: config/database.sql
   ```

3. **Configure .env:**
   ```
   - Edite config/.env
   - Atualize credenciais do BD
   - Adicione chaves Stripe
   - Defina APP_ENV=production
   - Defina APP_DEBUG=false
   ```

4. **Teste:**
   ```
   https://seu-dominio.com/refactor/index.php
   ```

### Domínio Customizado (Opcional)

No hPanel:
1. Domínios > Gerenciar
2. Adicione subdomínio: `teste.seuconto.com.br`
3. Aponte para: `/public_html/refactor`

---

## 🐛 Troubleshooting

### Erro: "Arquivo .env não encontrado"

**Causa:** Arquivo .env não existe ou sem permissões

**Solução:**
```bash
chmod 644 config/.env
```

### Erro: "Conexão com banco de dados falhou"

**Causa:** Credenciais incorretas no .env

**Solução:**
1. Verifique usuário e senha
2. Teste conexão no phpMyAdmin
3. Verifique se DB_HOST é `localhost`

### Erro: "Call to undefined function env()"

**Causa:** Arquivo `env.php` não foi incluído

**Solução:**
```php
require_once __DIR__ . '/config/env.php';
```

### Stripe não está funcionando

**Causa:** Chaves de teste/produção incorretas

**Solução:**
1. Verifique chaves no Dashboard Stripe
2. Modo teste: use `sk_test_...`
3. Modo produção: use `sk_live_...`

### Sessão não persiste

**Causa:** Configuração de sessão do servidor

**Solução:**
```php
// Adicione no início do session.php
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);
```

---

## 📊 Preços Padrão

| Item | Preço Original | Desconto | Preço Final |
|------|----------------|----------|-------------|
| E-book | R$ 49,90 | 40% OFF | **R$ 29,90** |
| Livro de Colorir | R$ 19,90 | 50% OFF | **R$ 9,90** |

---

## 📞 Suporte

Para dúvidas ou problemas:

- 📧 Email: contato@seuconto.com.br
- 💬 WhatsApp: (11) 99999-9999
- 📝 Issues: GitHub (se aplicável)

---

## 📝 Licença

Proprietary - © 2026 Seu Conto. Todos os direitos reservados.

---

## 🎯 Próximos Passos

- [ ] Implementar geração de PDF com histórias
- [ ] Integrar IA para personalização
- [ ] Adicionar Supabase para storage
- [ ] Implementar cópias físicas
- [ ] Sistema de cupons de desconto
- [ ] Painel administrativo
- [ ] Analytics e métricas

---

**Versão:** 1.0.0
**Última Atualização:** Janeiro 2026
**Autor:** Equipe Seu Conto
