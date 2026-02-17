# React Transfer Pack — Seu Conto

Este pacote contém tudo que um agente ou desenvolvedor precisa para reconstruir o frontend da plataforma **Seu Conto** do zero em **Vite + React 18**, sem precisar acessar o código PHP original.

---

## O que é o Seu Conto?

Plataforma web de livros infantis personalizados com IA. O usuário envia uma foto da criança, escolhe um tema, paga via Stripe, e recebe um PDF personalizado gerado por n8n + IA em até 30 minutos.

**Backend:** PHP 8.1 + MySQL + Apache (permanece intocado)
**Frontend novo:** Vite + React 18 SPA consumindo as APIs PHP existentes

---

## Documentos nesta pasta

| Arquivo | Conteúdo | Quando usar |
|---|---|---|
| `01-migration-plan.md` | Arquitetura completa, estrutura de pastas, mapeamento de rotas, stack, configuração | **Comece aqui.** Visão geral de toda a migração |
| `02-api-contracts.md` | Todos os endpoints PHP: método, request, response exatos, erros, notas de implementação | Ao implementar chamadas de API e tipos de resposta |
| `03-content-copy.md` | Todo o texto da interface em português: hero, FAQ, depoimentos, formulários, labels | Ao construir componentes com conteúdo real |
| `04-design-system.md` | Tokens de design (cores, tipografia, sombras, espaçamento), CSS do `main.css` convertido, config Tailwind | Ao configurar o design system e estilizar componentes |
| `05-business-rules.md` | TypeScript interfaces prontas, validações, regras de fluxo, guardas de rota, helpers | Ao implementar lógica de negócio e tipos |

---

## Como usar este pack

### Para um agente de IA

Forneça **todos os 5 documentos** (além deste README) como contexto. A ordem de implementação recomendada:

1. Ler `01-migration-plan.md` para entender a arquitetura geral
2. Ler `05-business-rules.md` para copiar os tipos TypeScript direto para `src/types/`
3. Ler `04-design-system.md` para configurar Tailwind e o design system
4. Ler `02-api-contracts.md` ao implementar cada módulo de API
5. Ler `03-content-copy.md` ao construir cada componente de UI

---

## Setup inicial (comandos)

```bash
# Criar o projeto
npm create vite@latest seu-conto-frontend -- --template react-ts
cd seu-conto-frontend

# Instalar dependências
npm install react-router-dom axios @tanstack/react-query zustand \
  react-hook-form zod @hookform/resolvers \
  react-dropzone @stripe/stripe-js recharts react-helmet-async

npm install -D tailwindcss @tailwindcss/vite @vitejs/plugin-react typescript \
  @types/react @types/react-dom

# Inicializar Tailwind
npx tailwindcss init -p
```

---

## Variáveis de ambiente

Criar `.env` na raiz do projeto React:

```env
VITE_API_BASE_URL=/api
VITE_STRIPE_PUBLISHABLE_KEY=pk_live_...
VITE_APP_NAME=Seu Conto
```

> Nunca expor `STRIPE_SECRET_KEY`, `DB_PASSWORD` ou `N8N_WEBHOOK_URL` no frontend.

---

## Proxy de desenvolvimento

Em dev, o React roda em `localhost:5173` e o PHP em `localhost:8000`. Configurar proxy em `vite.config.ts`:

```typescript
server: {
  proxy: {
    '/api': { target: 'http://localhost:8000', changeOrigin: true },
    '/uploads': { target: 'http://localhost:8000', changeOrigin: true },
  },
},
```

Isso mantém os cookies de sessão PHP (`PHPSESSID`) funcionando durante o desenvolvimento.

---

## Deploy

O build do Vite (`dist/`) é servido pelo Apache junto ao PHP:

```
/var/www/html/
├── dist/           ← build do React (estático)
├── api/            ← endpoints PHP (mantidos)
├── uploads/        ← arquivos gerados (mantidos)
└── .htaccess       ← redireciona tudo para dist/index.html
                      exceto /api/* e /uploads/*
```

Adicionar ao `.htaccess`:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !^/api/
RewriteCond %{REQUEST_URI} !^/uploads/
RewriteRule ^ /dist/index.html [L]
```

---

## Decisões arquiteturais já tomadas

| Decisão | Escolha | Motivo |
|---|---|---|
| State global | Zustand | Simples, sem boilerplate, suporta persist |
| Wizard state | `sessionStorage` via Zustand persist | Limpa ao fechar o browser (mesmo comportamento atual) |
| Auth state | `localStorage` via Zustand persist | "Lembrar de mim" funcional |
| Data fetching | React Query (TanStack) | Polling declarativo com `refetchInterval` |
| Formulários | React Hook Form + Zod | Validação tipada, performance |
| Upload | react-dropzone | Drag-drop nativo, sem dependência pesada |
| Charts | Recharts | Baseado em SVG, simples, sem CDN externo |
| Stripe | @stripe/stripe-js | Oficial, sem CDN inline |
| CSS | Tailwind CSS | Token-based, design system configurável |
| Routing | React Router v6 | Padrão da indústria |
| HTTP | Axios | Interceptors para 401, FormData mais simples |

---

## Pontos de atenção críticos

1. **`withCredentials: true`** no Axios é obrigatório — sem isso os cookies de sessão PHP não são enviados e todas as rotas autenticadas retornam 401.

2. **Sequência do wizard** é rígida: upload foto → criar pedido → polling → checkout. Não pular etapas.

3. **O polling PARA** quando status for `completed`, `failed` ou `cancelled`. Para outros status continua a cada 3 segundos.

4. **Preços são definidos pelo servidor** com base no `product_type`. O frontend exibe mas não calcula.

5. **Tema `espaco`** (sem acento no backend) — atenção ao usar como chave de objeto ou parâmetro de URL.

6. **Sessão PHP server-side** — não existe JWT ou token no header. A autenticação é 100% baseada em cookie. O store Zustand é apenas o espelho local do estado de sessão.

7. **`book_file_url`** só está preenchida quando `status === 'completed'`. Para outros status, o campo é `null`.
