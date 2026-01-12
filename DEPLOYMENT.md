# 🚀 Guia de Deployment - Seu Conto

Este documento contém instruções completas para deploy da aplicação em produção.

## 📋 Pré-requisitos

### Servidor

- **PHP**: 8.1 ou superior
- **MySQL**: 5.7 ou superior (ou MariaDB 10.3+)
- **Apache**: 2.4+ com mod_rewrite habilitado
- **Extensões PHP necessárias**:
  - PDO
  - PDO_MySQL
  - GD ou Imagick
  - mbstring
  - fileinfo
  - curl
  - json

### Serviços Externos

- Conta Stripe (para pagamentos)
- Servidor n8n (para geração de livros com IA)
- Servidor SMTP (para envio de emails)

---

## 🔧 Instalação

### 1. Clonar o Repositório

```bash
cd /var/www/
git clone <url-do-repositorio> seuconto
cd seuconto
```

### 2. Configurar Permissões

```bash
# Permissões de diretórios
chmod 755 -R .

# Permissão de escrita para uploads
chmod 775 uploads/temp
chmod 775 uploads/books

# Propriedade do Apache
chown -R www-data:www-data .
```

### 3. Configurar Variáveis de Ambiente

Copie o arquivo de exemplo e configure:

```bash
cp config/.env.example config/.env
nano config/.env
```

**Variáveis obrigatórias:**

```ini
# Ambiente
APP_ENV=production
APP_DEBUG=false
BASE_URL=https://seudominio.com

# Banco de Dados
DB_HOST=localhost
DB_NAME=seuconto_db
DB_USER=seuconto_user
DB_PASSWORD=senha_super_segura

# Stripe
STRIPE_PUBLISHABLE_KEY=pk_live_...
STRIPE_SECRET_KEY=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

# n8n
N8N_WEBHOOK_URL=https://n8n.seudominio.com/webhook/...
N8N_API_KEY=sua_chave_secreta

# SMTP
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=seu-email@gmail.com
SMTP_PASSWORD=sua_senha_app
SMTP_FROM=noreply@seudominio.com
SMTP_FROM_NAME=Seu Conto

# Segurança
CLEANUP_SECRET_KEY=chave-secreta-para-cleanup
SESSION_LIFETIME=7200
```

### 4. Importar Banco de Dados

```bash
mysql -u seuconto_user -p seuconto_db < database/schema.sql
```

### 5. Configurar Apache

**VirtualHost recomendado:**

```apache
<VirtualHost *:80>
    ServerName seudominio.com
    ServerAlias www.seudominio.com

    DocumentRoot /var/www/seuconto

    <Directory /var/www/seuconto>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/seuconto_error.log
    CustomLog ${APACHE_LOG_DIR}/seuconto_access.log combined
</VirtualHost>
```

**Habilitar módulos necessários:**

```bash
a2enmod rewrite
a2enmod headers
a2enmod expires
a2enmod deflate
systemctl restart apache2
```

### 6. Configurar SSL (Let's Encrypt)

```bash
apt install certbot python3-certbot-apache
certbot --apache -d seudominio.com -d www.seudominio.com
```

### 7. Configurar Cronjobs

```bash
crontab -e
```

Adicione:

```cron
# Limpeza de arquivos temporários (diariamente às 3h)
0 3 * * * /usr/bin/php /var/www/seuconto/cleanup-temp-files.php >> /var/log/seuconto-cleanup.log 2>&1

# Backup do banco de dados (diariamente às 2h)
0 2 * * * /usr/bin/mysqldump -u seuconto_user -p'senha' seuconto_db | gzip > /backups/seuconto_$(date +\%Y\%m\%d).sql.gz
```

---

## 🔐 Segurança

### Checklist de Segurança

- [ ] Alterar todas as senhas padrão
- [ ] Configurar `.env` com credenciais reais
- [ ] Desabilitar `display_errors` no PHP
- [ ] Habilitar HTTPS (SSL/TLS)
- [ ] Configurar firewall (UFW ou similar)
- [ ] Configurar backup automático
- [ ] Revisar permissões de arquivos
- [ ] Testar proteção de diretórios sensíveis
- [ ] Configurar rate limiting (Cloudflare ou similar)
- [ ] Monitorar logs de erro

### Testar Proteções

```bash
# Tentar acessar arquivos protegidos (deve retornar 403/404)
curl -I https://seudominio.com/config/config.php
curl -I https://seudominio.com/includes/auth.php
curl -I https://seudominio.com/.env
curl -I https://seudominio.com/cleanup-temp-files.php
```

---

## 📊 Monitoramento

### Logs Importantes

```bash
# Logs do Apache
tail -f /var/log/apache2/seuconto_error.log

# Logs PHP
tail -f /var/log/php_errors.log

# Logs de upload
tail -f /var/www/seuconto/uploads.log
```

### Métricas Recomendadas

- Taxa de uploads bem-sucedidos
- Tempo médio de geração de livros
- Taxa de conversão de checkout
- Erros 500 (deve ser 0%)
- Tempo de resposta médio

---

## 🔄 Atualização

### Deploy de Nova Versão

```bash
cd /var/www/seuconto

# Backup antes de atualizar
cp -r . ../seuconto_backup_$(date +%Y%m%d)

# Puxar últimas mudanças
git pull origin main

# Limpar cache (se houver)
rm -rf cache/*

# Reiniciar Apache
systemctl restart apache2
```

---

## 🐛 Troubleshooting

### Upload de Fotos Não Funciona

```bash
# Verificar permissões
ls -la uploads/temp/

# Verificar tamanho máximo
php -i | grep upload_max_filesize
php -i | grep post_max_size

# Ajustar se necessário
nano /etc/php/8.1/apache2/php.ini
```

### Erros 500

```bash
# Ver últimos erros
tail -n 50 /var/log/apache2/seuconto_error.log

# Ativar modo debug temporariamente
nano config/.env
# Alterar: APP_DEBUG=true
```

### n8n Não Responde

```bash
# Testar webhook manualmente
curl -X POST https://n8n.seudominio.com/webhook/...  \
  -H "Content-Type: application/json" \
  -d '{"test": true}'
```

### Emails Não Enviam

```bash
# Testar SMTP
telnet smtp.gmail.com 587

# Verificar logs
tail -f /var/log/mail.log
```

---

## 📞 Suporte

Para problemas técnicos:
- Verificar logs de erro
- Consultar documentação no `/uploads/README.md`
- Abrir issue no repositório

---

## 🎯 Performance

### Otimizações Recomendadas

1. **CDN**: Servir assets via Cloudflare
2. **OPcache**: Habilitar cache de PHP
3. **Redis**: Cache de sessões
4. **Image Optimization**: Comprimir uploads
5. **Database**: Índices em colunas pesquisadas

### Configurar OPcache

```ini
# /etc/php/8.1/apache2/php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
```

---

## ✅ Checklist Final

Antes de ir para produção:

- [ ] Todas as variáveis `.env` configuradas
- [ ] Banco de dados criado e populado
- [ ] SSL/HTTPS configurado
- [ ] Cronjobs configurados
- [ ] Backups automáticos configurados
- [ ] Webhooks Stripe testados
- [ ] Webhook n8n testado
- [ ] Email de teste enviado com sucesso
- [ ] Upload de foto testado
- [ ] Criação de livro end-to-end testada
- [ ] Checkout Stripe testado
- [ ] Logs monitorados
- [ ] DNS configurado corretamente
- [ ] Firewall configurado

---

**Data de última atualização:** Janeiro 2026
**Versão:** 1.0.0
