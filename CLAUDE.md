# 🤖 Claude Code - Contexto do Projeto


 


Este arquivo contém informações essenciais para o Claude Code entender e trabalhar com este projeto.


 


---


 


## 📋 Visão Geral do Projeto


 


**Nome:** Seu Conto


**Tipo:** Plataforma web de livros infantis personalizados com IA


**Stack:** PHP 8.1+ (Vanilla), MySQL, Apache


**Arquitetura:** MVC Simplificado com Services Layer


 


### Propósito


Criar livros infantis personalizados onde a criança é transformada em personagem usando IA. O usuário envia uma foto, escolhe um tema, paga via Stripe, e recebe um PDF personalizado gerado por n8n + IA.


 


---


 


## 🏗️ Arquitetura


 


### Padrão MVC Simplificado


```


┌─────────────┐


│   View      │ ← components/ (HTML reutilizável)


│   (PHP)     │ ← pages/ (controllers/rotas)


└─────────────┘


       ↓


┌─────────────┐


│  Services   │ ← services/ (lógica de negócio)


│   (PHP)     │   - OrderService


└─────────────┘   - N8nService


       ↓           - PaymentService


┌─────────────┐   - AnalyticsService


│   Model     │


│  (MySQL)    │ ← Acesso via PDO nos Services


└─────────────┘


```


 


### Camadas do Sistema


 


#### 1. **Apresentação (View)**


- **components/**: Blocos reutilizáveis de HTML


  - `landing/`: Seções da landing page


  - `admin/`: Componentes do dashboard admin


  - `head.php`, `header.php`, `footer.php`: Layout base


- **pages/**: Controladores de página


  - `auth/`: Login e registro


  - `create/`: Wizard de 4 etapas


  - `admin/`: Dashboard administrativo


  - `legal/`: Termos e privacidade


  - `dashboard.php`: Área do usuário


 


#### 2. **Lógica de Negócio (Services)**


- **OrderService.php**: CRUD de pedidos


- **N8nService.php**: Integração com n8n (geração de livros)


- **PaymentService.php**: Integração Stripe


- **AnalyticsService.php**: Métricas e KPIs


 


#### 3. **APIs (JSON Endpoints)**


- **Wizard**: `create-order.php`, `upload-photo.php`, `check-order-status.php`


- **Pagamento**: `create-checkout-session.php`, `stripe-webhook.php`


- **n8n**: `n8n-callback.php`


- **Auth**: `auth.php`, `logout.php`


 


#### 4. **Configuração**


- **config/config.php**: Constantes públicas (temas, preços)


- **config/db.php**: Conexão PDO com MySQL


- **config/permissions.php**: Sistema ACL (roles e permissões)


- **config/env.php**: Loader de variáveis `.env`


 


#### 5. **Utilidades**


- **includes/auth.php**: Gestão de sessão


- **includes/admin-middleware.php**: Proteção de rotas admin


- **includes/functions.php**: 50+ helpers (e(), url(), formatPrice())


- **includes/mailer.php**: Envio de emails via SMTP


 


---


 


## 🔄 Fluxo Principal (User Journey)


 


### Criação de Livro (Happy Path)


 


```


1. Landing Page (index.php)


   ↓ [Clica "Criar Livro"]


 


2. Step 1: Escolha de Tema (pages/create/step1-theme.php)


   ↓ [Seleciona tema: aventura, fantasia, ciencia, natureza, espaco]


 


3. Step 2: Upload e Dados (pages/create/step2-photo.php)


   ↓ [Faz upload de foto via drag-drop]


   → API: upload-photo.php


   ↓ [Preenche nome da criança e idade]


   → API: create-order.php (cria pedido com status='pending')


 


4. Step 3: Processamento (pages/create/step3-processing.php)


   → API: create-checkout-session.php (cria sessão Stripe)


   ↓ [Redireciona para Stripe Checkout]


   → [USUÁRIO PAGA]


   ↓ [Stripe confirma pagamento]


 


5. Webhook Stripe (api/stripe-webhook.php)


   → Valida signature


   → Atualiza pedido: status='processing'


   → **DISPARA n8n**: N8nService::triggerBookGeneration()


 


6. n8n Gera Livro com IA


   → Recebe: foto, nome, idade, tema


   → Processa: IA transforma foto em ilustração


   → Gera: PDF personalizado


   → Callback: api/n8n-callback.php


 


7. Callback n8n (api/n8n-callback.php)


   → Valida callback


   → Atualiza pedido: status='completed', book_file_url


   → Envia email: sendBookReadyEmail()


 


8. Frontend Polling (creation-flow.js)


   → A cada 3s: check-order-status.php


   → Detecta status='completed'


   → Redireciona para Step 4


 


9. Step 4: Download (pages/create/step4-checkout.php)


   ✅ Exibe link de download do PDF


```


 


---


 


## 📂 Estrutura de Diretórios


 


```


seuconto/


├── api/                        # Endpoints JSON


│   ├── auth.php               # Autenticação de usuário


│   ├── logout.php             # Logout


│   ├── upload-photo.php       # Upload de fotos (validação robusta)


│   ├── create-order.php       # Criar pedido no banco


│   ├── create-checkout-session.php  # Stripe checkout


│   ├── stripe-webhook.php     # Webhook Stripe (dispara n8n)


│   ├── n8n-callback.php       # Callback n8n (livro pronto)


│   └── check-order-status.php # Polling de status


│


├── assets/


│   ├── css/


│   │   ├── main.css           # Estilos globais do site


│   │   ├── admin.css          # Dashboard admin (900+ linhas)


│   │   ├── auth.css           # Login/registro


│   │   └── dashboard.css      # Dashboard usuário


│   ├── js/


│   │   ├── main.js            # Scripts globais


│   │   ├── creation-flow.js   # Wizard 4 etapas (550 linhas)


│   │   ├── slider.js          # Comparador antes/depois (200 linhas)


│   │   ├── auth.js            # Login/registro


│   │   ├── checkout.js        # Stripe integration


│   │   └── admin-charts.js    # Gráficos admin (Chart.js)


│   ├── img/


│   └── fonts/


│


├── components/                 # Blocos HTML reutilizáveis


│   ├── landing/


│   │   ├── hero.php


│   │   ├── how-it-works.php


│   │   ├── themes.php


│   │   ├── testimonials.php


│   │   ├── faq.php


│   │   └── cta-final.php


│   ├── admin/


│   │   ├── sidebar.php


│   │   ├── topbar.php


│   │   └── kpi-card.php


│   ├── head.php               # Meta tags, SEO, CSS imports


│   ├── header.php             # Navbar principal


│   └── footer.php             # Rodapé


│


├── config/


│   ├── config.php             # Constantes públicas (AVAILABLE_THEMES, PRICE_EBOOK)


│   ├── db.php                 # getDBConnection() via PDO


│   ├── env.php                # Carrega .env


│   ├── permissions.php        # ACL: hasPermission(), requireAdmin()


│   └── .env                   # Variáveis secretas (gitignored)


│


├── includes/


│   ├── auth.php               # isLoggedIn(), getCurrentUser(), requireAuth()


│   ├── admin-middleware.php   # protectAdminRoute(), logAdminAccess()


│   ├── functions.php          # e(), url(), formatPrice(), formatDate()


│   └── mailer.php             # sendEmail(), sendBookReadyEmail()


│


├── pages/


│   ├── auth/


│   │   ├── login.php          # Login de usuários


│   │   └── register.php       # Registro de novos usuários


│   ├── create/                # Wizard de 4 etapas


│   │   ├── step1-theme.php    # Seleção de tema


│   │   ├── step2-photo.php    # Upload foto + dados


│   │   ├── step3-processing.php  # Tela de espera (polling)


│   │   └── step4-checkout.php # Checkout Stripe / Download


│   ├── admin/


│   │   ├── login.php          # Login de admins


│   │   ├── index.php          # Dashboard principal


│   │   ├── orders/            # Gerenciar pedidos


│   │   ├── leads/             # Gerenciar leads


│   │   ├── ai-monitor/        # Monitor de geração IA


│   │   └── settings/          # Configurações


│   ├── legal/


│   │   ├── terms.php          # Termos de uso (estrutura pronta)


│   │   └── privacy.php        # Política de privacidade (estrutura pronta)


│   └── dashboard.php          # Dashboard do usuário (meus livros)


│


├── services/                   # Lógica de negócio


│   ├── OrderService.php       # createOrder(), getUserOrders(), updateOrderStatus()


│   ├── N8nService.php         # triggerBookGeneration(), validateCallback()


│   ├── PaymentService.php     # createCheckoutSession(), handleWebhook()


│   └── AnalyticsService.php   # getMainKPIs(), getOrdersChart()


│


├── uploads/                    # Arquivos enviados (gitignored)


│   ├── temp/                  # Fotos temporárias (auto-delete 24h)


│   └── books/                 # PDFs gerados


│


├── .htaccess                   # Segurança, cache, proteção de diretórios


├── .gitignore                  # Ignora .env, uploads/*, node_modules


├── index.php                   # Landing page (modular)


├── 404.php                     # Página de erro 404


├── 500.php                     # Página de erro 500


├── cleanup-temp-files.php      # Script de limpeza (cronjob)


├── DEPLOYMENT.md               # Guia de deploy completo


├── README-DEV.md               # Guia de desenvolvimento


└── README.md                   # Documentação principal


```


 


---


 


## 🔐 Sistema de Autenticação e Permissões


 


### Roles (Papéis)


- **guest**: Visitante não autenticado


- **user**: Usuário registrado (pode criar livros)


- **admin**: Administrador (acesso ao dashboard)


- **super_admin**: Super administrador (acesso total)


 


### Funções de Auth (includes/auth.php)


```php


isLoggedIn()           // Verifica se está autenticado


getCurrentUser()       // Retorna array com dados do usuário


setUserSession($user)  // Cria sessão


requireAuth()          // Redireciona se não logado


isAdmin()              // Verifica se é admin


```


 


### Proteção de Rotas


```php


// Proteger página de usuário


require_once __DIR__ . '/includes/auth.php';


requireAuth(); // Redireciona para login se não autenticado


 


// Proteger rota admin


require_once __DIR__ . '/includes/admin-middleware.php';


protectAdminRoute('admin'); // Requer role 'admin' ou superior


```


 


---


 


## 💾 Banco de Dados


 


### Tabelas Principais


 


#### **users**


```sql


- id (PK)


- name


- email (unique)


- password (bcrypt)


- role (user|admin|super_admin)


- created_at


```


 


#### **orders**


```sql


- id (PK)


- user_id (FK → users.id)


- child_name


- child_age


- theme (aventura|fantasia|ciencia|natureza|espaco)


- product_type (ebook|physical)


- child_photo_url


- book_file_url


- status (pending|processing|completed|failed|cancelled)


- amount (em centavos)


- stripe_payment_intent_id


- metadata (JSON)


- created_at


- updated_at


```


 


#### **leads** (opcional)


```sql


- id (PK)


- email


- name


- child_name


- theme


- source


- created_at


```


 


### Conexão com Banco


```php


require_once __DIR__ . '/config/db.php';


$pdo = getDBConnection(); // Retorna PDO instance


 


// Sempre usar prepared statements


$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");


$stmt->execute([$orderId]);


$order = $stmt->fetch(PDO::FETCH_ASSOC);


```


 


---


 


## 🎨 Temas Disponíveis


 


Definidos em `config/config.php` como constante `AVAILABLE_THEMES`:


 


```php


'aventura' => [


    'name' => 'Aventura',


    'description' => 'Explorações emocionantes',


    'icon' => 'compass',


    'color' => '#FF6B6B'


],


'fantasia' => [


    'name' => 'Fantasia',


    'description' => 'Mundos mágicos e encantados',


    'icon' => 'sparkles',


    'color' => '#9B59B6'


],


// ... mais temas


```


 


---


 


## 🔧 Integrações Externas


 


### 1. Stripe (Pagamentos)


- **Checkout Session**: Criado via `PaymentService::createCheckoutSession()`


- **Webhook**: Recebido em `api/stripe-webhook.php`


- **Validação**: Signature validation obrigatória


- **Eventos tratados**:


  - `checkout.session.completed` → Dispara n8n


  - `payment_intent.payment_failed` → Marca pedido como failed


 


### 2. n8n (Geração de Livros com IA)


- **Trigger**: Disparado APENAS após pagamento confirmado


- **Método**: `N8nService::triggerBookGeneration()`


- **Payload**: order_id, child_name, photo_url, theme, metadata


- **Callback**: n8n retorna para `api/n8n-callback.php` quando pronto


- **Validação**: Token/signature validation


 


### 3. SMTP (Emails)


- **Biblioteca**: PHPMailer (abstração em `includes/mailer.php`)


- **Tipos de email**:


  - `sendWelcomeEmail()`: Boas-vindas


  - `sendOrderConfirmationEmail()`: Confirmação de pedido


  - `sendBookReadyEmail()`: Livro pronto para download


 


---


 


## 🛡️ Segurança


 


### Validações Implementadas


 


#### Upload de Fotos


```php


// api/upload-photo.php


- Autenticação obrigatória


- Tipos permitidos: JPEG, PNG, WebP (validação via finfo)


- Tamanho máximo: 5MB


- Dimensões mínimas: 200x200px


- Nome único: user_id + timestamp + random


- Permissões: 644


```


 


#### APIs


```php


- Autenticação via isLoggedIn()


- Validação de propriedade (user_id === order.user_id)


- Prepared statements (SQL Injection)


- JSON input validation


- HTTP method validation (POST/GET)


```


 


#### Proteção de Arquivos (.htaccess)


```apache


# Bloqueia acesso direto


- /config/


- /includes/


- /services/


- .env


- cleanup-temp-files.php


```


 


#### Headers de Segurança


```apache


X-XSS-Protection: 1; mode=block


X-Content-Type-Options: nosniff


X-Frame-Options: SAMEORIGIN


```


 


---


 


## 📊 Assets JavaScript


 


### creation-flow.js (Wizard Manager)


- **Responsabilidade**: Gerenciar fluxo de 4 etapas


- **Persistência**: localStorage (`creation_flow_data`)


- **Funcionalidades**:


  - Step 1: Seleção de tema


  - Step 2: Upload com drag-drop + validação


  - Step 3: Polling de status (3s interval)


  - Step 4: Checkout Stripe


- **Métodos principais**:


  - `handleFileUpload()`: Upload assíncrono


  - `createOrder()`: Cria pedido via API


  - `startStatusPolling()`: Verifica status


 


### slider.js (Image Comparison)


- **Responsabilidade**: Slider antes/depois


- **Interatividade**: Mouse, touch, teclado


- **Acessibilidade**: ARIA labels completos


- **Uso**: Landing page (transformação de fotos)


 


### admin-charts.js (Dashboard Charts)


- **Biblioteca**: Chart.js 4.4.0


- **Gráficos**:


  - `initOrdersChart()`: Linha (pedidos + receita)


  - `initThemesChart()`: Pizza (temas populares)


- **Exportação**: CSV via `exportChartToCSV()`


 


---


 


## 🧪 Testes Manuais


 


### Testar Upload


```bash


curl -X POST http://localhost/api/upload-photo.php \


  -F "photo=@test-image.jpg" \


  -H "Cookie: PHPSESSID=seu_session_id"


```


 


### Testar Criação de Pedido


```bash


curl -X POST http://localhost/api/create-order.php \


  -H "Content-Type: application/json" \


  -H "Cookie: PHPSESSID=seu_session_id" \


  -d '{


    "theme": "aventura",


    "child_name": "João",


    "child_age": 5,


    "photo_file": "photo_1_123456_abc.jpg"


  }'


```


 


### Testar Limpeza de Arquivos


```bash


php cleanup-temp-files.php


```


 


---


 


## 🚀 Comandos Úteis


 


### Desenvolvimento


```bash


# Servidor PHP built-in


php -S localhost:8000


 


# Verificar sintaxe


php -l arquivo.php


 


# Ver logs Apache


tail -f /var/log/apache2/error.log


```


 


### Git


```bash


# Ver histórico de fases


git log --oneline --grep="FASE"


 


# Verificar branch atual


git branch


 


# Status do working directory


git status


```


 


### MySQL


```bash


# Conectar ao banco


mysql -u seuconto -p seuconto_db


 


# Ver estrutura de tabela


DESCRIBE orders;


 


# Contar pedidos


SELECT COUNT(*) FROM orders;


```


 


---


 


## 🐛 Troubleshooting Comum


 


### Upload não funciona


1. Verificar permissões: `chmod 775 uploads/temp/`


2. Verificar upload_max_filesize: `php -i | grep upload`


3. Ver logs: `tail -f /var/log/apache2/error.log`


 


### Webhook Stripe não chama


1. Verificar URL no dashboard Stripe


2. Verificar logs: `tail api/stripe-webhook.php`


3. Testar manualmente com Stripe CLI


 


### n8n não dispara


1. Verificar N8N_WEBHOOK_URL no .env


2. Testar webhook manualmente: `curl -X POST URL`


3. Verificar logs: `grep n8n /var/log/apache2/error.log`


 


### Sessão não persiste


1. Verificar session_start() em auth.php


2. Verificar permissões de /tmp/


3. Ver php.ini: session.save_path


 


---


 


## 📝 Convenções de Código


 


### Nomenclatura


- **Classes**: PascalCase (`OrderService`)


- **Funções**: camelCase (`getUserOrders()`)


- **Arquivos**: kebab-case (`admin-middleware.php`)


- **Constantes**: UPPER_SNAKE_CASE (`PRICE_EBOOK`)


 


### Estrutura de Função


```php


/**


 * Descrição breve da função


 *


 * @param int $orderId ID do pedido


 * @return array Dados do pedido


 * @throws Exception Se pedido não encontrado


 */


public static function getOrder($orderId) {


    // Validação


    if (!$orderId) {


        throw new Exception('ID inválido');


    }


 


    // Lógica


    $pdo = getDBConnection();


    // ...


 


    // Retorno


    return $order;


}


```


 


### Helpers Sempre Usar


```php


e($str)              // Escape HTML (XSS protection)


url($path)           // Gerar URL completa


formatPrice($cents)  // R$ 29,90


formatDate($date)    // dd/mm/yyyy


```


 


---


 


## 🎯 Próximas Features (Roadmap)


 


- [ ] Sistema de cupons de desconto


- [ ] Livros físicos (integração com gráfica)


- [ ] Mais temas (espaço, dinossauros, etc)


- [ ] Preview do livro antes do pagamento


- [ ] Avaliações e comentários


- [ ] Compartilhamento em redes sociais


- [ ] App mobile (React Native)


- [ ] Painel de analytics avançado


- [ ] Sistema de afiliados


 


---


 


## 📞 Pontos de Contato


 


### Quando modificar código, considerar:


 


**Adicionar novo tema:**


1. `config/config.php` → AVAILABLE_THEMES


2. `assets/css/main.css` → Cores do tema


3. Backend já suporta automaticamente


 


**Adicionar novo status de pedido:**


1. `services/OrderService.php` → Validar novo status


2. `api/check-order-status.php` → Mensagem + progresso


3. `assets/js/creation-flow.js` → Tratamento no frontend


 


**Modificar fluxo de pagamento:**


1. `api/create-checkout-session.php` → Sessão Stripe


2. `api/stripe-webhook.php` → Processar evento


3. `services/PaymentService.php` → Lógica de pagamento


 


---


 


## 🔄 Última Atualização


 


**Data:** Janeiro 2026


**Versão:** 1.0.0


**Branch:** `claude/plan-code-refactoring-V0MRC`


**Status:** ✅ Produção Ready


 


---


 


**Este arquivo deve ser mantido atualizado sempre que houver mudanças significativas na arquitetura ou fluxos do sistema.**
