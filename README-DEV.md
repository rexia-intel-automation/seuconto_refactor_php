# 👨‍💻 Guia de Desenvolvimento - Seu Conto

Guia para configurar ambiente de desenvolvimento local.

## 🚀 Quick Start

### 1. Requisitos

- PHP 8.1+
- MySQL 5.7+ ou MariaDB 10.3+
- Apache 2.4+ ou PHP built-in server
- Composer (opcional)
- Git

### 2. Instalação Local

```bash
# Clonar repositório
git clone <url-do-repo> seuconto
cd seuconto

# Copiar arquivo de ambiente
cp config/.env.example config/.env

# Editar configurações locais
nano config/.env
```

### 3. Configurar Banco de Dados

```sql
CREATE DATABASE seuconto_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'seuconto'@'localhost' IDENTIFIED BY 'senha123';
GRANT ALL PRIVILEGES ON seuconto_dev.* TO 'seuconto'@'localhost';
FLUSH PRIVILEGES;
```

```bash
# Importar schema
mysql -u seuconto -p seuconto_dev < database/schema.sql
```

### 4. Servidor de Desenvolvimento

```bash
# Opção 1: Apache
# Configure virtual host apontando para o diretório do projeto

# Opção 2: PHP Built-in Server
php -S localhost:8000
```

Acesse: `http://localhost:8000`

---

## 📁 Estrutura do Projeto

```
seuconto/
├── api/                    # Endpoints JSON
│   ├── upload-photo.php
│   ├── auth.php
│   └── ...
├── assets/                 # Assets públicos
│   ├── css/
│   ├── js/
│   └── img/
├── components/             # Componentes reutilizáveis
│   ├── landing/           # Blocos da landing page
│   ├── admin/             # Componentes admin
│   ├── head.php
│   ├── header.php
│   └── footer.php
├── config/                 # Configurações
│   ├── config.php         # Constantes públicas
│   ├── db.php             # Conexão DB
│   ├── env.php            # Carregar .env
│   ├── permissions.php    # ACL
│   └── .env               # Variáveis de ambiente (gitignored)
├── includes/               # Lógica auxiliar
│   ├── auth.php           # Autenticação
│   ├── admin-middleware.php
│   ├── functions.php      # Helpers
│   └── mailer.php         # Email
├── pages/                  # Páginas
│   ├── auth/              # Login/Registro
│   ├── create/            # Wizard de criação
│   ├── admin/             # Dashboard admin
│   ├── legal/             # Termos e privacidade
│   └── dashboard.php      # Dashboard do usuário
├── services/               # Camada de serviços
│   ├── OrderService.php
│   ├── N8nService.php
│   ├── PaymentService.php
│   └── AnalyticsService.php
├── uploads/                # Arquivos enviados (gitignored)
│   ├── temp/              # Fotos temporárias
│   └── books/             # PDFs gerados
├── .htaccess              # Configuração Apache
├── .gitignore
├── index.php              # Landing page
├── cleanup-temp-files.php # Script de limpeza
├── DEPLOYMENT.md          # Guia de deploy
└── README.md              # Documentação principal
```

---

## 🛠️ Desenvolvimento

### Boas Práticas

1. **Sempre use includes/functions.php helpers:**
   ```php
   e($str);           // Escapar HTML
   url($path);        // Gerar URL
   formatPrice($cents); // Formatar preço
   ```

2. **Sempre use services para lógica de negócio:**
   ```php
   OrderService::createOrder($data);
   OrderService::getUserOrders($userId);
   ```

3. **Sempre valide entrada do usuário:**
   ```php
   $name = trim($_POST['name'] ?? '');
   if (empty($name)) {
       // erro
   }
   ```

4. **Sempre use PDO prepared statements:**
   ```php
   $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
   $stmt->execute([$userId]);
   ```

### Fluxo de Autenticação

```php
// Proteger página
require_once __DIR__ . '/includes/auth.php';
requireAuth(); // Redireciona se não logado

// Obter usuário logado
$user = getCurrentUser();

// Proteger rota admin
require_once __DIR__ . '/includes/admin-middleware.php';
protectAdminRoute('admin');
```

### Upload de Fotos

```javascript
const formData = new FormData();
formData.append('photo', fileInput.files[0]);

const response = await fetch('/api/upload-photo.php', {
    method: 'POST',
    body: formData
});

const result = await response.json();
if (result.success) {
    console.log('URL:', result.data.url);
}
```

---

## 🧪 Testing

### Testar Upload

```bash
curl -X POST http://localhost:8000/api/upload-photo.php \
  -F "photo=@test-image.jpg" \
  -H "Cookie: PHPSESSID=..."
```

### Testar Limpeza de Arquivos

```bash
php cleanup-temp-files.php
```

---

## 🐛 Debug

### Habilitar Modo Debug

```ini
# config/.env
APP_DEBUG=true
```

### Ver Erros PHP

```php
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### Console do Navegador

```javascript
// Verificar dados do wizard
console.log(localStorage.getItem('creation_flow_data'));

// Limpar dados do wizard
localStorage.removeItem('creation_flow_data');
```

---

## 📚 Recursos Úteis

- **Documentação PHP**: https://php.net
- **Stripe API**: https://stripe.com/docs/api
- **n8n Docs**: https://docs.n8n.io
- **Chart.js**: https://chartjs.org

---

## 🔄 Git Workflow

```bash
# Criar branch para feature
git checkout -b feature/nome-da-feature

# Fazer commits
git add .
git commit -m "Descrição clara da mudança"

# Push e criar PR
git push origin feature/nome-da-feature
```

### Mensagens de Commit

Use prefixos claros:
- `feat:` Nova funcionalidade
- `fix:` Correção de bug
- `refactor:` Refatoração
- `docs:` Documentação
- `style:` Formatação/estilo
- `test:` Testes

---

## ❓ FAQ

**P: Como adicionar um novo tema?**
```php
// config/config.php
define('AVAILABLE_THEMES', [
    // ... existentes
    'espaco' => [
        'name' => 'Espaço',
        'description' => 'Aventuras intergalácticas',
        'icon' => 'rocket',
        'color' => '#4338CA'
    ]
]);
```

**P: Como adicionar um novo admin?**
```sql
UPDATE users SET role = 'admin' WHERE email = 'admin@example.com';
```

**P: Onde ficam os logs?**
- Apache: `/var/log/apache2/error.log`
- PHP: `error_log()` vai para apache error log
- Upload: verificar permissões em `uploads/`

---

## 🎯 Roadmap

- [ ] Sistema de cupons de desconto
- [ ] Livros físicos (integração com gráfica)
- [ ] Mais temas
- [ ] App mobile
- [ ] Painel de analytics avançado

---

**Happy Coding! 🚀**
