# API Contracts — Seu Conto

Todos os endpoints são PHP e permanecem intocados. O frontend React consome via Axios com `withCredentials: true` (cookie de sessão PHP).

**Base URL:** `/api` (relativo ao domínio)
**Autenticação:** Cookie `PHPSESSID` (mantido automaticamente pelo browser)
**Formato de erro padrão:** `{ success: false, error: "mensagem" }`
**Formato de sucesso padrão:** `{ success: true, message: "mensagem", data: {...} }`

---

## Autenticação — `POST /api/auth.php`

O campo `action` no body determina a operação.

### Registro

**Request:**
```json
{
  "action": "register",
  "fullName": "Maria Silva",
  "email": "maria@exemplo.com",
  "phone": "(11) 99999-9999",
  "password": "senha123"
}
```

**Validações do servidor:**
- `fullName`: obrigatório, não vazio após sanitize
- `email`: válido por `FILTER_VALIDATE_EMAIL`
- `phone`: obrigatório, não vazio
- `password`: mínimo 6 caracteres
- Email não pode estar já cadastrado

**Response 200 (sucesso):**
```json
{
  "success": true,
  "message": "Conta criada com sucesso!",
  "data": { "userId": 42 }
}
```

**Response 400 (erro):**
```json
{ "success": false, "error": "Email já cadastrado" }
```
```json
{ "success": false, "error": "Dados inválidos" }
```

---

### Login

**Request:**
```json
{
  "action": "login",
  "email": "maria@exemplo.com",
  "password": "senha123"
}
```

**Response 200 (sucesso):**
```json
{
  "success": true,
  "message": "Login realizado com sucesso!",
  "data": { "userId": 42 }
}
```

**Response 400 (erro):**
```json
{ "success": false, "error": "Email ou senha incorretos" }
```
```json
{ "success": false, "error": "Email e senha são obrigatórios" }
```

> Após login bem-sucedido, o servidor seta o cookie `PHPSESSID`. O React **não** recebe nem armazena token; a sessão é server-side.

---

### Logout

**Request:**
```json
{ "action": "logout" }
```

**Response 200:**
```json
{ "success": true, "message": "Logout realizado com sucesso", "data": null }
```

---

## Upload de Foto — `POST /api/upload-photo.php`

**Content-Type:** `multipart/form-data`
**Requer autenticação:** sim (401 se não logado)

**Request:**
```
FormData:
  photo: File (JPEG | PNG | WebP)
```

**Validações do servidor:**
- MIME type via `finfo_file()`: `image/jpeg`, `image/png`, `image/webp`
- Tamanho máximo: **5MB** (5.242.880 bytes)
- Dimensões mínimas: **200×200 px**
- Arquivo deve ser imagem válida (`getimagesize()`)

**Response 200 (sucesso):**
```json
{
  "success": true,
  "message": "Foto enviada com sucesso!",
  "data": {
    "filename": "photo_42_1706000000_a1b2c3d4e5f6g7h8.jpg",
    "path": "uploads/temp/photo_42_1706000000_a1b2c3d4e5f6g7h8.jpg",
    "url": "https://seuconto.com.br/uploads/temp/photo_42_1706000000_a1b2c3d4e5f6g7h8.jpg",
    "size": 1048576,
    "mime_type": "image/jpeg",
    "dimensions": {
      "width": 1024,
      "height": 768
    }
  }
}
```

**Respostas de erro:**
```json
{ "success": false, "error": "Tipo de arquivo não permitido. Use JPEG, PNG ou WebP." }
{ "success": false, "error": "O arquivo é muito grande. Tamanho máximo: 5MB." }
{ "success": false, "error": "A imagem é muito pequena. Tamanho mínimo: 200x200 pixels." }
{ "success": false, "error": "Arquivo não é uma imagem válida" }
{ "success": false, "error": "Nenhum arquivo foi enviado" }
```

> O `filename` retornado é o que deve ser enviado para `create-order.php` no campo `photo_file`.

---

## Criar Pedido — `POST /api/create-order.php`

**Content-Type:** `application/json`
**Requer autenticação:** sim

**Request:**
```json
{
  "theme": "aventura",
  "child_name": "João",
  "child_age": 5,
  "photo_file": "photo_42_1706000000_a1b2c3d4e5f6g7h8.jpg",
  "product_type": "ebook"
}
```

**Campos:**
| Campo | Tipo | Obrigatório | Validação |
|---|---|---|---|
| `theme` | string | sim | deve estar em `AVAILABLE_THEMES` |
| `child_name` | string | sim | não vazio |
| `child_age` | number | sim | inteiro entre 1 e 12 |
| `photo_file` | string | sim | nome do arquivo retornado pelo upload |
| `product_type` | string | não | `"ebook"` (default) ou `"physical"` |

**Preços determinados pelo servidor:**
- `ebook` → R$ 29,90 (2990 centavos)
- `physical` → R$ 49,90 (4990 centavos)

**Response 200 (sucesso):**
```json
{
  "success": true,
  "message": "Pedido criado com sucesso!",
  "data": {
    "order_id": 123,
    "amount": 2990,
    "product_type": "ebook",
    "status": "pending"
  }
}
```

**Respostas de erro:**
```json
{ "success": false, "error": "Campo obrigatório ausente: child_name" }
{ "success": false, "error": "Tema inválido" }
{ "success": false, "error": "Idade deve estar entre 1 e 12 anos" }
```

---

## Criar Sessão de Checkout — `POST /api/create-checkout-session.php`

**Content-Type:** `application/json`
**Requer autenticação:** sim

**Request:**
```json
{ "order_id": 123 }
```

**Validações:**
- Pedido deve existir
- Pedido deve pertencer ao usuário autenticado
- Status do pedido deve ser `"pending"` (já pago retorna erro)

**Response 200 (sucesso):**
```json
{
  "success": true,
  "data": {
    "session_id": "cs_test_a1b2c3d4...",
    "session_url": "https://checkout.stripe.com/pay/cs_test_a1b2c3d4..."
  }
}
```

> Após receber `session_url`, redirecionar o usuário diretamente para essa URL **ou** usar `stripe.redirectToCheckout({ sessionId })`.

**Respostas de erro:**
```json
{ "success": false, "error": "order_id é obrigatório" }
{ "success": false, "error": "Pedido não encontrado" }
{ "success": false, "error": "Você não tem permissão para acessar este pedido" }
{ "success": false, "error": "Este pedido já foi processado" }
```

---

## Verificar Status do Pedido — `GET /api/check-order-status.php`

**Requer autenticação:** sim
**Query param:** `?order_id=123`

**Response 200:**
```json
{
  "success": true,
  "data": {
    "order_id": 123,
    "status": "processing",
    "status_message": "Criando sua história mágica com IA... ✨",
    "progress": 50,
    "book_file_url": null,
    "created_at": "2026-02-17 10:30:00",
    "updated_at": "2026-02-17 10:31:00",
    "child_name": "João",
    "theme": "aventura"
  }
}
```

**Mapeamento status → progress → message:**
| status | progress | status_message |
|---|---|---|
| `pending` | 10 | "Aguardando pagamento..." |
| `processing` | 50 | "Criando sua história mágica com IA... ✨" |
| `completed` | 100 | "Livro pronto! Preparando download..." |
| `failed` | 0 | "Ops! Algo deu errado na geração." |
| `cancelled` | 0 | "Pedido cancelado." |

> Quando `status === "completed"`, o campo `book_file_url` conterá a URL do PDF para download.
> O polling deve parar quando status for `completed`, `failed` ou `cancelled`.

**Respostas de erro:**
```json
{ "success": false, "error": "order_id é obrigatório" }  // HTTP 400
{ "success": false, "error": "Pedido não encontrado" }   // HTTP 404
{ "success": false, "error": "Você não tem permissão para acessar este pedido" }  // HTTP 403
```

---

## Webhook Stripe — `POST /api/stripe-webhook.php`

> Este endpoint **não é chamado pelo frontend**. É chamado diretamente pelo Stripe.
> Documentado aqui apenas para entendimento do fluxo de pagamento.

Após `checkout.session.completed`, o Stripe chama esse endpoint que:
1. Atualiza o pedido para `status = "processing"`
2. Dispara o n8n para gerar o livro

---

## Callback n8n — `POST /api/n8n-callback.php`

> Este endpoint **não é chamado pelo frontend**. É chamado pelo n8n quando o livro fica pronto.

Quando o n8n termina de gerar o PDF:
1. Atualiza o pedido para `status = "completed"` + `book_file_url`
2. Envia email para o usuário

---

## Notas de Implementação

### Axios config recomendada
```typescript
const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL ?? '/api',
  withCredentials: true,  // CRÍTICO: mantém o cookie PHPSESSID
  headers: { 'Content-Type': 'application/json' },
});

// Interceptor de erro global
api.interceptors.response.use(
  (res) => res,
  (err) => {
    if (err.response?.status === 401) {
      // Limpar store de auth e redirecionar para login
      useAuthStore.getState().logout();
      window.location.href = '/login';
    }
    return Promise.reject(err);
  }
);
```

### Upload de foto (FormData — não usar JSON)
```typescript
const uploadPhoto = async (file: File) => {
  const form = new FormData();
  form.append('photo', file);
  // NÃO setar Content-Type manualmente — o browser seta com boundary correto
  return api.post('/upload-photo.php', form);
};
```

### Sequência do wizard (ordem obrigatória)
```
1. uploadPhoto(file)           → obtém filename
2. createOrder({ ...dados, photo_file: filename })  → obtém order_id
3. polling checkOrderStatus(order_id) a cada 3s    → aguarda "completed"
4. createCheckoutSession(order_id)                 → obtém session_url
5. window.location.href = session_url              → redireciona para Stripe
```
