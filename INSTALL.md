# 🚀 Guia de Instalação Rápida - Seu Conto

## Correções de Layout Aplicadas ✅

Este projeto teve as seguintes correções aplicadas para garantir que o layout funcione corretamente:

### 1. **Sistema de Paths Dinâmico**
- ✅ Criado arquivo `config/paths.php` com detecção automática de caminhos
- ✅ Funções auxiliares: `url()`, `asset()`, `redirectTo()`
- ✅ Suporte para desenvolvimento local e produção
- ✅ Funciona em subpasta (`/refactor/`) ou na raiz do domínio

### 2. **CSS Corrigido**
- ✅ Adicionada animação `slideOutRight` para toasts
- ✅ Adicionados estilos para `.user-avatar`
- ✅ Adicionados estilos para `.animate-on-scroll`
- ✅ Todas as variáveis CSS definidas corretamente

### 3. **JavaScript Atualizado**
- ✅ `BASE_PATH` dinâmico configurado
- ✅ API URLs usando caminho correto
- ✅ Animações ao scroll funcionando

### 4. **Arquivos Atualizados**
- ✅ `includes/header.php` - Usando funções de path
- ✅ `includes/footer.php` - Usando funções de path
- ✅ `index.php` - Links corrigidos
- ✅ `assets/js/main.js` - Paths dinâmicos
- ✅ `assets/css/main.css` - Animações e estilos adicionados

### 5. **Segurança e Performance**
- ✅ Arquivo `.htaccess` criado com:
  - Proteção de arquivos sensíveis
  - Compressão GZIP
  - Cache de recursos estáticos
  - Headers de segurança

---

## 📋 Pré-requisitos

- PHP 8.0 ou superior
- MySQL 8.0 ou superior
- Apache com mod_rewrite habilitado
- Composer (para instalar dependências)

---

## 🔧 Instalação

### Passo 1: Clone ou Faça Upload do Projeto

```bash
# Em desenvolvimento local
git clone <seu-repositorio>
cd seuconto_refactor_php

# Ou faça upload via FTP para seu servidor
```

### Passo 2: Configure o Ambiente

1. **Crie o arquivo `.env`** na pasta `config/`:

```bash
cp config/.env.example config/.env
```

2. **Edite o arquivo** `config/.env` com suas credenciais:

```env
# Database
DB_HOST=localhost
DB_NAME=seu_banco
DB_USER=seu_usuario
DB_PASSWORD=sua_senha
DB_CHARSET=utf8mb4

# Stripe
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLISHABLE_KEY=pk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Application
APP_NAME="Seu Conto"
APP_URL=https://seu-dominio.com
APP_ENV=production
APP_DEBUG=false
```

### Passo 3: Configure o Banco de Dados

Execute o arquivo SQL:

```bash
mysql -u seu_usuario -p seu_banco < config/database.sql
```

Ou via phpMyAdmin:
1. Acesse phpMyAdmin
2. Selecione seu banco de dados
3. Clique em "Importar"
4. Selecione `config/database.sql`

### Passo 4: Instale as Dependências (Composer)

```bash
composer require stripe/stripe-php
```

### Passo 5: Configure o .htaccess

**Para desenvolvimento em subpasta:**

Edite `.htaccess` linha 11:
```apache
RewriteBase /sua-subpasta/
```

**Para produção na raiz:**

Deixe como está:
```apache
RewriteBase /
```

### Passo 6: Configure Permissões

```bash
# Linux/Mac
chmod 755 .
chmod 644 .htaccess
chmod 600 config/.env

# Garanta que o diretório de uploads seja gravável (quando criar)
# mkdir uploads
# chmod 775 uploads
```

---

## 🌐 Configuração de Paths

O sistema detecta automaticamente o caminho base da aplicação. Você pode forçar um caminho específico de 3 formas:

### Opção 1: Variável de Ambiente (Recomendado)

No arquivo `config/.env`:
```env
BASE_PATH=/refactor
```

### Opção 2: Servidor Web

No arquivo `.htaccess`:
```apache
SetEnv BASE_PATH /refactor
```

### Opção 3: Detecção Automática (Padrão)

O sistema detecta automaticamente baseado em `$_SERVER['SCRIPT_NAME']`.

---

## 🧪 Testando a Instalação

1. **Acesse a landing page:**
   - Local: `http://localhost/sua-subpasta/index.php`
   - Produção: `https://seu-dominio.com/index.php`

2. **Verifique se o CSS está carregando:**
   - A página deve aparecer com o tema lilás e amarelo
   - Fontes Google (Fredoka e Nunito) devem estar aplicadas

3. **Teste a navegação:**
   - Clique nos links do menu
   - Verifique se os CTAs funcionam

4. **Verifique o console do navegador:**
   - Pressione F12
   - Aba "Console"
   - Não deve haver erros 404

---

## 🐛 Solução de Problemas

### Problema: CSS não carrega (página sem estilo)

**Solução:**
1. Verifique se `assets/css/main.css` existe
2. Abra o console do navegador (F12)
3. Veja se há erro 404 para o CSS
4. Ajuste o `BASE_PATH` conforme necessário

### Problema: Links quebrados (404)

**Solução:**
1. Verifique se mod_rewrite está habilitado
2. Teste o caminho manualmente
3. Ajuste o `.htaccess` RewriteBase

### Problema: Erro de banco de dados

**Solução:**
1. Verifique credenciais no `config/.env`
2. Certifique-se que o banco existe
3. Execute o SQL novamente

### Problema: Animações não funcionam

**Solução:**
1. Verifique se `assets/js/main.js` está carregando
2. Abra o console e veja se há erros JavaScript
3. Limpe o cache do navegador (Ctrl+F5)

---

## 📁 Estrutura de Arquivos

```
seuconto_refactor_php/
├── .htaccess              # Configuração Apache ✅
├── index.php              # Landing page ✅
├── api/                   # Endpoints REST
│   ├── auth.php
│   └── checkout.php
├── assets/                # Recursos estáticos
│   ├── css/
│   │   └── main.css       # CSS principal ✅
│   └── js/
│       └── main.js        # JavaScript principal ✅
├── config/                # Configurações
│   ├── .env              # Variáveis de ambiente (criar)
│   ├── database.php
│   ├── database.sql
│   ├── paths.php         # Sistema de paths ✅
│   └── stripe.php
├── includes/              # Componentes compartilhados
│   ├── header.php        # Header global ✅
│   ├── footer.php        # Footer global ✅
│   ├── functions.php
│   └── session.php
└── pages/                 # Páginas da aplicação
    ├── auth/
    ├── criar.php
    ├── checkout.php
    └── dashboard.php
```

---

## 🎨 Personalização

### Cores (OKLch)

Edite `assets/css/main.css`:

```css
:root {
    --color-primary: oklch(0.70 0.15 280);    /* Lilás */
    --color-secondary: oklch(0.88 0.18 85);   /* Amarelo */
    /* Altere os valores conforme desejar */
}
```

### Fontes

Edite a linha 11 de `assets/css/main.css`:

```css
@import url('https://fonts.googleapis.com/css2?family=SuaFonte:wght@300;400;600&display=swap');
```

---

## 📞 Suporte

Se encontrar problemas após seguir este guia:

1. Verifique o console do navegador (F12)
2. Verifique os logs de erro do PHP
3. Certifique-se que todos os arquivos foram criados corretamente

---

## ✅ Checklist de Instalação

- [ ] PHP 8.0+ instalado
- [ ] MySQL configurado
- [ ] Arquivo `.env` criado e configurado
- [ ] Banco de dados importado (`database.sql`)
- [ ] Composer instalado
- [ ] Stripe SDK instalado (`composer require stripe/stripe-php`)
- [ ] `.htaccess` configurado corretamente
- [ ] Permissões ajustadas
- [ ] CSS carregando corretamente
- [ ] JavaScript funcionando
- [ ] Links e navegação funcionando
- [ ] Animações ao scroll ativas

---

**Instalação completa! 🎉**

Agora sua aplicação deve estar funcionando com layout perfeito e todos os recursos ativos.
