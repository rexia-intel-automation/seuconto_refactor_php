# Migração para Vite + React — Seu Conto

**Data:** Fevereiro 2026
**Branch de destino:** `claude/document-pages-vite-react-9FJQS`
**Status:** Planejamento

---

## Visão Geral

Este documento mapeia toda a estrutura atual em PHP para uma nova SPA (Single Page Application) usando **Vite + React 18**, consumindo os mesmos endpoints da API PHP que já existem no backend. O backend PHP permanece intocado; apenas o frontend é substituído.

---

## Stack Tecnológica

| Camada | Atual | Novo |
|---|---|---|
| Build | Nenhum (PHP server-side) | Vite 5 |
| UI Framework | PHP + HTML inline | React 18 |
| Roteamento | Apache/PHP files | React Router v6 |
| Estado Global | `$_SESSION` + `localStorage` | Zustand |
| Estilização | CSS puro (`main.css`, etc.) | Tailwind CSS 3 |
| HTTP Client | `fetch` manual em JS | Axios + React Query |
| Pagamentos | Stripe.js CDN inline | `@stripe/react-stripe-js` |
| Gráficos | Chart.js CDN inline | Recharts |
| Formulários | Validação JS manual | React Hook Form + Zod |

---

## Estrutura de Pastas Proposta

```
seu-conto-frontend/
├── public/
│   ├── favicon.ico
│   └── assets/
│       └── img/               # imagens estáticas (copiar de assets/img/)
│
├── src/
│   ├── main.tsx               # ponto de entrada, React.StrictMode
│   ├── App.tsx                # roteador raiz
│   │
│   ├── api/                   # clientes de API (Axios)
│   │   ├── auth.ts
│   │   ├── orders.ts
│   │   ├── upload.ts
│   │   ├── checkout.ts
│   │   └── admin.ts
│   │
│   ├── components/            # componentes reutilizáveis
│   │   ├── layout/
│   │   │   ├── Header.tsx
│   │   │   ├── Footer.tsx
│   │   │   └── AdminSidebar.tsx
│   │   ├── ui/
│   │   │   ├── Button.tsx
│   │   │   ├── Badge.tsx
│   │   │   ├── Toast.tsx
│   │   │   ├── Modal.tsx
│   │   │   ├── KpiCard.tsx
│   │   │   └── LoadingSpinner.tsx
│   │   ├── landing/
│   │   │   ├── HeroSection.tsx
│   │   │   ├── HowItWorks.tsx
│   │   │   ├── ThemesSection.tsx
│   │   │   ├── Testimonials.tsx
│   │   │   ├── FaqSection.tsx
│   │   │   └── CtaFinal.tsx
│   │   └── wizard/
│   │       ├── WizardProgress.tsx
│   │       ├── ThemeCard.tsx
│   │       ├── PhotoUpload.tsx
│   │       └── OrderStatusPoller.tsx
│   │
│   ├── hooks/                 # custom hooks
│   │   ├── useAuth.ts
│   │   ├── useOrderStatus.ts
│   │   └── useToast.ts
│   │
│   ├── pages/                 # uma por rota
│   │   ├── LandingPage.tsx
│   │   ├── auth/
│   │   │   ├── LoginPage.tsx
│   │   │   └── RegisterPage.tsx
│   │   ├── create/
│   │   │   ├── Step1ThemePage.tsx
│   │   │   ├── Step2PhotoPage.tsx
│   │   │   ├── Step3ProcessingPage.tsx
│   │   │   └── Step4CheckoutPage.tsx
│   │   ├── DashboardPage.tsx
│   │   ├── legal/
│   │   │   ├── TermsPage.tsx
│   │   │   └── PrivacyPage.tsx
│   │   ├── admin/
│   │   │   ├── AdminLoginPage.tsx
│   │   │   ├── AdminDashboardPage.tsx
│   │   │   ├── AdminOrdersPage.tsx
│   │   │   ├── AdminOrderDetailPage.tsx
│   │   │   ├── AdminLeadsPage.tsx
│   │   │   ├── AdminAiMonitorPage.tsx
│   │   │   └── AdminSettingsPage.tsx
│   │   ├── NotFoundPage.tsx
│   │   └── ErrorPage.tsx
│   │
│   ├── store/                 # Zustand stores
│   │   ├── authStore.ts
│   │   └── wizardStore.ts
│   │
│   ├── types/                 # TypeScript interfaces
│   │   ├── order.ts
│   │   ├── user.ts
│   │   └── api.ts
│   │
│   └── utils/
│       ├── formatters.ts      # formatPrice, formatDate, etc.
│       └── validators.ts      # email, phone, password strength
│
├── index.html
├── vite.config.ts
├── tailwind.config.ts
├── tsconfig.json
└── package.json
```

---

## Mapeamento de Rotas

### Rotas Públicas

| PHP (arquivo) | React Route | Componente/Página |
|---|---|---|
| `index.php` | `/` | `LandingPage.tsx` |
| `pages/auth/login.php` | `/login` | `auth/LoginPage.tsx` |
| `pages/auth/register.php` | `/register` | `auth/RegisterPage.tsx` |
| `pages/legal/terms.php` | `/termos` | `legal/TermsPage.tsx` |
| `pages/legal/privacy.php` | `/privacidade` | `legal/PrivacyPage.tsx` |
| `404.php` | `*` (catch-all) | `NotFoundPage.tsx` |

### Rotas Protegidas (usuário autenticado)

| PHP (arquivo) | React Route | Componente/Página |
|---|---|---|
| `pages/create/step1-theme.php` | `/criar/tema` | `create/Step1ThemePage.tsx` |
| `pages/create/step2-photo.php` | `/criar/foto` | `create/Step2PhotoPage.tsx` |
| `pages/create/step3-processing.php` | `/criar/processando` | `create/Step3ProcessingPage.tsx` |
| `pages/create/step4-checkout.php` | `/criar/checkout` | `create/Step4CheckoutPage.tsx` |
| `pages/dashboard.php` | `/minha-conta` | `DashboardPage.tsx` |

### Rotas Admin

| PHP (arquivo) | React Route | Componente/Página |
|---|---|---|
| `pages/admin/login.php` | `/admin/login` | `admin/AdminLoginPage.tsx` |
| `pages/admin/index.php` | `/admin` | `admin/AdminDashboardPage.tsx` |
| `pages/admin/orders/` | `/admin/pedidos` | `admin/AdminOrdersPage.tsx` |
| `pages/admin/orders/view.php` | `/admin/pedidos/:id` | `admin/AdminOrderDetailPage.tsx` |
| `pages/admin/leads/` | `/admin/leads` | `admin/AdminLeadsPage.tsx` |
| `pages/admin/ai-monitor/` | `/admin/monitor-ia` | `admin/AdminAiMonitorPage.tsx` |
| `pages/admin/settings/` | `/admin/configuracoes` | `admin/AdminSettingsPage.tsx` |

---

## Mapeamento de Componentes

### Layout

| PHP (componente) | React Component | Notas |
|---|---|---|
| `components/head.php` | Gerenciado pelo Vite + `<title>` dinâmico | Usar `react-helmet-async` para meta tags por página |
| `components/header.php` | `layout/Header.tsx` | Detectar `useAuth()` para exibir avatar ou botão de login |
| `components/footer.php` | `layout/Footer.tsx` | Botão WhatsApp fixo mantido como componente separado |
| `components/admin/sidebar.php` | `layout/AdminSidebar.tsx` | `useLocation()` do React Router para active state |
| `components/admin/topbar.php` | `layout/AdminTopbar.tsx` | — |
| `components/admin/kpi-card.php` | `ui/KpiCard.tsx` | Props tipadas com TypeScript interface |

### Landing Page

| PHP (componente) | React Component | Notas |
|---|---|---|
| `components/landing/hero.php` | `landing/HeroSection.tsx` | — |
| `components/landing/how-it-works.php` | `landing/HowItWorks.tsx` | — |
| `components/landing/themes.php` | `landing/ThemesSection.tsx` | Temas vêm da API ou constante local |
| `components/landing/testimonials.php` | `landing/Testimonials.tsx` | — |
| `components/landing/faq.php` | `landing/FaqSection.tsx` | `<details>` substituído por componente com `useState` |
| `components/landing/cta-final.php` | `landing/CtaFinal.tsx` | — |

### Wizard de Criação

| PHP (página) | React Component | Notas |
|---|---|---|
| Barra de progresso inline | `wizard/WizardProgress.tsx` | Props: `currentStep`, `totalSteps` |
| Cards de tema (step1) | `wizard/ThemeCard.tsx` | Props: `theme`, `selected`, `onSelect` |
| Drag-drop upload (step2) | `wizard/PhotoUpload.tsx` | Usar `react-dropzone` |
| Polling de status (step3) | `wizard/OrderStatusPoller.tsx` / hook `useOrderStatus.ts` | `useQuery` com `refetchInterval` do React Query |

---

## Estado da Aplicação

### Substituição do localStorage / sessionStorage do Wizard

O wizard atual usa `localStorage` para tema (step1) e `sessionStorage` para foto + dados da criança (step2). No React, isso é centralizado em um **Zustand store** com `persist`:

```typescript
// src/store/wizardStore.ts
interface WizardState {
  theme: string | null;
  photo: { filename: string; url: string } | null;
  childName: string;
  childAge: number | null;
  childGender: string;
  characteristics: string;
  dedication: string;
  orderId: number | null;

  setTheme: (theme: string) => void;
  setPhoto: (photo: WizardState['photo']) => void;
  setChildData: (data: Partial<WizardState>) => void;
  setOrderId: (id: number) => void;
  reset: () => void;
}
```

Usar `persist` do Zustand com `sessionStorage` para que os dados sejam limpos ao fechar o navegador (mesmo comportamento atual).

### Autenticação

Substituição do `$_SESSION` PHP:

```typescript
// src/store/authStore.ts
interface AuthState {
  user: { id: number; name: string; email: string; role: string } | null;
  isLoggedIn: boolean;
  login: (user: AuthState['user']) => void;
  logout: () => void;
}
```

- Usar `persist` com `localStorage` (equivalente ao "Lembrar de mim").
- O hook `useAuth.ts` expõe `user`, `isLoggedIn`, `isAdmin`, `login`, `logout`.
- A sessão PHP continua sendo a fonte de verdade no servidor; o store React é apenas o espelho no cliente.

### Proteção de Rotas

Criar componentes `<ProtectedRoute>` e `<AdminRoute>`:

```tsx
// ProtectedRoute.tsx
const ProtectedRoute = ({ children }) => {
  const { isLoggedIn } = useAuth();
  return isLoggedIn ? children : <Navigate to="/login" replace />;
};

// AdminRoute.tsx
const AdminRoute = ({ children }) => {
  const { user } = useAuth();
  const isAdmin = ['admin', 'super_admin'].includes(user?.role ?? '');
  return isAdmin ? children : <Navigate to="/admin/login" replace />;
};
```

---

## Consumo das APIs

Todos os endpoints PHP existentes são mantidos sem alteração. O frontend React faz `fetch`/Axios para os mesmos caminhos.

### Configuração do Axios

```typescript
// src/api/client.ts
import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL ?? '/api',
  withCredentials: true,   // manter cookie de sessão PHP
  headers: { 'Content-Type': 'application/json' },
});
```

### Módulos de API

#### `api/auth.ts`
```typescript
export const login = (email: string, password: string) =>
  api.post('/auth.php', { action: 'login', email, password });

export const register = (data: RegisterPayload) =>
  api.post('/auth.php', { action: 'register', ...data });

export const logout = () =>
  api.post('/auth.php', { action: 'logout' });
```

#### `api/upload.ts`
```typescript
export const uploadPhoto = (file: File) => {
  const form = new FormData();
  form.append('photo', file);
  return api.post('/upload-photo.php', form, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
};
```

#### `api/orders.ts`
```typescript
export const createOrder = (data: CreateOrderPayload) =>
  api.post('/create-order.php', data);

export const checkOrderStatus = (orderId: number) =>
  api.get(`/check-order-status.php?order_id=${orderId}`);

export const createCheckoutSession = (orderId: number) =>
  api.post('/create-checkout-session.php', { order_id: orderId });
```

### Polling com React Query

Substituição do `setInterval` manual do `creation-flow.js`:

```typescript
// src/hooks/useOrderStatus.ts
export const useOrderStatus = (orderId: number | null) =>
  useQuery({
    queryKey: ['order-status', orderId],
    queryFn: () => checkOrderStatus(orderId!),
    enabled: !!orderId,
    refetchInterval: (data) => {
      const status = data?.data?.data?.status;
      if (['completed', 'failed', 'cancelled'].includes(status)) return false;
      return 3000; // STATUS_POLLING_INTERVAL
    },
  });
```

---

## Wizard de 4 Etapas

### Fluxo de Navegação

```
/criar/tema  →  /criar/foto  →  /criar/processando?order_id=X  →  /criar/checkout?order_id=X
```

- `Step1ThemePage`: ao selecionar tema, chama `wizardStore.setTheme()` e navega com `useNavigate()`.
- `Step2PhotoPage`: faz upload via `api/upload.ts`, armazena resultado no store, faz `createOrder`, navega para step 3 com `?order_id`.
- `Step3ProcessingPage`: lê `orderId` da query string (`useSearchParams`), usa `useOrderStatus` hook. Ao detectar `completed`, navega para step 4. Exibe barra de progresso visual separada do polling real.
- `Step4CheckoutPage`: carrega dados do pedido, exibe resumo, inicia Stripe Checkout via `@stripe/react-stripe-js`.

### Guardas de Etapa

Cada step verifica pré-condições e redireciona se necessário:

```typescript
// Step2PhotoPage.tsx
const { theme } = useWizardStore();
useEffect(() => {
  if (!theme) navigate('/criar/tema', { replace: true });
}, [theme]);
```

---

## Integração com Stripe

Substituição do `checkout.js` com Stripe.js CDN:

```tsx
// src/pages/create/Step4CheckoutPage.tsx
import { loadStripe } from '@stripe/stripe-js';

const stripePromise = loadStripe(import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY);

// ao clicar em "Pagar Agora":
const { data } = await createCheckoutSession(orderId);
const stripe = await stripePromise;
await stripe.redirectToCheckout({ sessionId: data.data.session_id });
```

---

## Gráficos Admin

Substituição do `admin-charts.js` com Chart.js CDN:

```tsx
// src/pages/admin/AdminDashboardPage.tsx
import {
  LineChart, Line, PieChart, Pie, Cell,
  XAxis, YAxis, Tooltip, ResponsiveContainer
} from 'recharts';

// ordersChartData e themesChartData vêm de GET /api/admin/analytics.php
```

---

## Variáveis de Ambiente (`.env` Vite)

```env
VITE_API_BASE_URL=https://seuconto.com.br/api
VITE_STRIPE_PUBLISHABLE_KEY=pk_live_...
VITE_APP_NAME=Seu Conto
```

> Variáveis Vite são expostas ao cliente com prefixo `VITE_`. Segredos (Stripe secret key, n8n token, DB) permanecem **apenas** no `.env` do PHP.

---

## Configuração do Vite

```typescript
// vite.config.ts
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  plugins: [react(), tailwindcss()],
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost:8000',  // PHP server
        changeOrigin: true,
      },
    },
  },
  build: {
    outDir: 'dist',
  },
});
```

Com o proxy do Vite, em desenvolvimento o React roda em `localhost:5173` e faz chamadas `/api/*` que são encaminhadas ao servidor PHP em `localhost:8000`, mantendo os cookies de sessão funcionando.

---

## Dependências (`package.json`)

```json
{
  "dependencies": {
    "react": "^18.3.0",
    "react-dom": "^18.3.0",
    "react-router-dom": "^6.26.0",
    "axios": "^1.7.0",
    "@tanstack/react-query": "^5.56.0",
    "zustand": "^4.5.0",
    "react-hook-form": "^7.52.0",
    "zod": "^3.23.0",
    "@hookform/resolvers": "^3.9.0",
    "react-dropzone": "^14.2.0",
    "@stripe/stripe-js": "^4.4.0",
    "recharts": "^2.12.0",
    "react-helmet-async": "^2.0.5"
  },
  "devDependencies": {
    "@vitejs/plugin-react": "^4.3.0",
    "vite": "^5.4.0",
    "typescript": "^5.5.0",
    "@types/react": "^18.3.0",
    "@types/react-dom": "^18.3.0",
    "tailwindcss": "^3.4.0",
    "@tailwindcss/vite": "^4.0.0"
  }
}
```

---

## Migrações de CSS

| CSS Atual | Estratégia no React |
|---|---|
| `assets/css/main.css` | Converter variáveis CSS para `tailwind.config.ts` + classes Tailwind |
| `assets/css/admin.css` | Componentes com classes Tailwind; manter estilos específicos em `AdminDashboard.module.css` se necessário |
| `assets/css/auth.css` | Tailwind puro nos componentes de auth |
| `assets/css/dashboard.css` | Tailwind puro no `DashboardPage.tsx` |

Variáveis CSS globais (cores, fontes) são mantidas no `tailwind.config.ts`:

```typescript
// tailwind.config.ts
theme: {
  extend: {
    colors: {
      primary: '#FF6B6B',
      secondary: '#9B59B6',
      // ...demais cores dos temas
    },
    fontFamily: {
      sans: ['Nunito', 'sans-serif'],
      display: ['Fredoka One', 'cursive'],
    },
  },
},
```

---

## Helpers Migrados

| PHP (`includes/functions.php`) | TypeScript (`src/utils/formatters.ts`) |
|---|---|
| `e($string)` | Não necessário — React escapa automaticamente o JSX |
| `formatPrice($cents)` | `formatPrice(cents: number): string` — `Intl.NumberFormat` |
| `formatDate($date)` | `formatDate(date: string): string` — `Intl.DateTimeFormat` |
| `isValidEmail($email)` | `isValidEmail(email: string): boolean` — regex ou Zod |
| `isValidPhone($phone)` | `isValidPhone(phone: string): boolean` |
| `timeAgo($datetime)` | `timeAgo(date: string): string` — `Intl.RelativeTimeFormat` |

---

## Ordem de Implementação Sugerida

1. **Setup base** — Vite + React + TS + Tailwind + React Router + Zustand + React Query
2. **API client** — Axios + módulos de API + interceptors de erro
3. **Auth** — store, hooks, páginas de login/registro, rotas protegidas
4. **Layout** — Header, Footer, AdminSidebar
5. **Landing Page** — todas as seções
6. **Wizard de criação** — 4 steps + hooks de polling
7. **Dashboard do usuário**
8. **Área admin** — dashboard + listagens + gráficos (Recharts)
9. **Páginas legais**
10. **Testes e-to-e** — Playwright ou Cypress cobrindo o fluxo principal

---

## Notas de Deploy

- O build do Vite (`dist/`) pode ser servido como arquivos estáticos pelo Apache junto ao PHP.
- Configurar `.htaccess` para redirecionar todas as rotas do frontend para `dist/index.html` (exceto `/api/*` e `/uploads/*`).
- As rotas PHP existentes em `api/`, `uploads/` e `config/` continuam sendo servidas diretamente pelo Apache/PHP.

```apache
# .htaccess — regras adicionais para o frontend React
RewriteEngine On

# Serve arquivos estáticos do dist/ diretamente
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !^/api/
RewriteCond %{REQUEST_URI} !^/uploads/
RewriteRule ^ /dist/index.html [L]
```

---

*Este documento deve ser atualizado à medida que decisões técnicas forem tomadas durante a implementação.*
