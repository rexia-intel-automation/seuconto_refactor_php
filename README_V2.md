# 📚 Seu Conto - Plataforma de Livros Infantis com IA

Este projeto é uma plataforma web desenvolvida em PHP (Vanilla) que permite a criação de livros infantis personalizados. A aplicação utiliza Inteligência Artificial para transformar fotos de crianças em ilustrações de personagens, inserindo-as em narrativas temáticas.

## 🚀 Visão Geral da Arquitetura

O projeto segue um padrão **MVC Simplificado** (Model-View-Controller) sem o uso de frameworks pesados, priorizando performance e facilidade de manutenção.

### Fluxo de Integração n8n (Segurança)
Para garantir que a URL do webhook do n8n não fique exposta no Frontend (JavaScript):
1. O cliente envia a foto e o nome para o endpoint local `/api/trigger-generation.php`.
2. O PHP valida a sessão e os arquivos.
3. O `N8nService.php` carrega a URL secreta do webhook via variáveis de ambiente (`config/env.php`).
4. O servidor realiza a requisição cURL para o n8n e retorna o status para o frontend.

---

## 📂 Estrutura de Diretórios

```text
seu-conto-app/
│
├── 📁 assets/                  # Arquivos estáticos públicos (Frontend)
│   ├── 📁 css/
│   │   ├── main.css            # Estilos do Site Público (Soft UI / Pastel)
│   │   └── admin.css           # Estilos do Dashboard Administrativo (Data-first)
│   ├── 📁 js/
│   │   ├── main.js             # Scripts globais e UI interativa
│   │   ├── slider.js           # Lógica do comparador "Antes/Depois"
│   │   ├── creation-flow.js    # Gerencia o Wizard de criação e envio p/ PHP
│   │   └── admin-charts.js     # Configuração de gráficos (Dashboard)
│   ├── 📁 img/                 # Imagens estáticas
│   │   ├── icons/              # Ícones SVG (Bento Grid)
│   │   └── placeholders/       # Imagens de exemplo
│   └── 📁 fonts/               # Fontes locais (Lato, Poppins)
│
├── 📁 config/                  # Configurações do Sistema
│   ├── db.php                  # Conexão PDO com Banco de Dados
│   ├── config.php              # Constantes globais públicas
│   ├── permissions.php         # Definição de ACL (Access Control List)
│   └── env.php                 # [GITIGNORE] Credenciais sensíveis e Webhooks n8n
│
├── 📁 components/              # Fragmentos de HTML (Views Reutilizáveis)
│   ├── 📁 landing/             # Blocos da Página Inicial
│   │   ├── hero.php
│   │   ├── transformation.php  # Slider Antes/Depois
│   │   ├── inside-book.php     # Mockup do livro aberto
│   │   ├── how-it-works.php    # Bento Grid
│   │   ├── social-impact.php
│   │   ├── roadmap.php
│   │   └── privacy.php
│   ├── 📁 admin/               # Blocos do Painel Administrativo
│   │   ├── sidebar.php
│   │   ├── topbar.php
│   │   ├── kpi-card.php
│   │   └── charts.php
│   ├── header.php              # Navbar Principal
│   ├── footer.php              # Rodapé Principal
│   └── head.php                # Meta tags e imports de CSS
│
├── 📁 includes/                # Lógica Auxiliar e Middlewares
│   ├── auth.php                # Gestão de Sessão de Usuário
│   ├── admin-middleware.php    # Proteção de rotas (verifica is_admin)
│   ├── functions.php           # Helpers (sanitize, format date)
│   └── mailer.php              # Configuração de e-mail (SMTP)
│
├── 📁 services/                # Regras de Negócio e Integrações (Models/Services)
│   ├── N8nService.php          # [PROXY] Conecta ao webhook n8n via cURL
│   ├── AnalyticsService.php    # Queries complexas para o Dashboard Admin
│   ├── PaymentService.php      # Integração com Gateway de Pagamento
│   └── OrderService.php        # Gestão de status de pedidos
│
├── 📁 pages/                   # Controladores de Página (Rotas visíveis)
│   ├── 📁 auth/
│   │   ├── login.php
│   │   └── register.php
│   │
│   ├── 📁 create/              # Fluxo de Criação (Wizard)
│   │   ├── step1-theme.php
│   │   ├── step2-photo.php
│   │   ├── step3-processing.php # Tela de espera (Polling de status)
│   │   └── step4-checkout.php
│   │
│   ├── 📁 admin/               # Área Restrita (Backoffice)
│   │   ├── login.php
│   │   ├── index.php           # Dashboard Geral
│   │   ├── 📁 orders/          # Gestão de Pedidos
│   │   ├── 📁 leads/           # Lista de Amostra Grátis
│   │   ├── 📁 ai-monitor/      # Logs de comunicação com n8n
│   │   └── 📁 settings/        # Configuração de preços e prompts
│   │
│   ├── 📁 legal/
│   │   ├── terms.php
│   │   └── privacy.php
│   │
│   └── dashboard.php           # Área do Cliente (Meus Pedidos)
│
├── 📁 api/                     # Endpoints AJAX (JSON Responses)
│   ├── trigger-generation.php  # [POST] Recebe foto/nome -> Chama N8nService -> Retorna ID
│   ├── check-status.php        # [GET] Verifica se o n8n concluiu o livro
│   ├── capture-lead.php        # [POST] Salva dados da Amostra Grátis
│   └── 📁 admin/               # Endpoints exclusivos do Dashboard
│       └── get-metrics.php     # Dados para os gráficos
│
├── 📁 uploads/                 # [GITIGNORE] Armazenamento de Arquivos
│   ├── 📁 temp/                # Fotos enviadas (excluídas via CronJob)
│   └── 📁 books/               # PDFs finais gerados
│
├── .htaccess                   # Configuração de Rotas e Segurança Apache
├── .gitignore                  # Arquivos ignorados (env.php, uploads, vendor)
├── composer.json               # Dependências (Opcional, se usar bibliotecas)
└── index.php                   # Entry Point (Landing Page)
