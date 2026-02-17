# Regras de Negócio e TypeScript Types — Seu Conto

---

## TypeScript Interfaces

Cole estes tipos em `src/types/` — são derivados diretamente das respostas reais das APIs PHP.

### `src/types/api.ts` — Envelope genérico de resposta

```typescript
export interface ApiSuccess<T = unknown> {
  success: true;
  message: string;
  data: T;
}

export interface ApiError {
  success: false;
  error: string;
}

export type ApiResponse<T = unknown> = ApiSuccess<T> | ApiError;
```

---

### `src/types/user.ts`

```typescript
export type UserRole = 'guest' | 'user' | 'admin' | 'super_admin';

export interface User {
  id: number;
  name: string;      // campo "full_name" na tabela, exposto como "name" na sessão
  email: string;
  phone: string;
  role: UserRole;
}

// Dados retornados no login/registro
export interface AuthData {
  userId: number;
}
```

---

### `src/types/order.ts`

```typescript
export type OrderStatus =
  | 'pending'       // aguardando pagamento
  | 'paid'          // pagamento confirmado, aguardando geração
  | 'generating'    // n8n gerando conteúdo
  | 'processing'    // processando livro final
  | 'completed'     // livro pronto, book_file_url preenchida
  | 'failed'        // falha na geração
  | 'refunded'      // reembolsado
  | 'cancelled';    // cancelado pelo usuário ou admin

export type ThemeKey =
  | 'aventura'
  | 'fantasia'
  | 'espaco'
  | 'animais'
  | 'princesa'
  | 'super-heroi';

export type ProductType = 'ebook' | 'physical';

export type Gender = 'masculino' | 'feminino' | 'outro';

export interface Theme {
  name: string;
  description: string;
  icon: string;
  color: string;
}

export const AVAILABLE_THEMES: Record<ThemeKey, Theme> = {
  aventura: {
    name: 'Aventura',
    description: 'Explorações emocionantes e descobertas',
    icon: 'compass',
    color: '#FF6B6B',
  },
  fantasia: {
    name: 'Fantasia',
    description: 'Mundo mágico com criaturas fantásticas',
    icon: 'sparkles',
    color: '#A78BFA',
  },
  espaco: {
    name: 'Espaço',
    description: 'Viagem intergaláctica pelo universo',
    icon: 'rocket',
    color: '#60A5FA',
  },
  animais: {
    name: 'Animais',
    description: 'Amigos animais e natureza',
    icon: 'paw',
    color: '#34D399',
  },
  princesa: {
    name: 'Princesa/Príncipe',
    description: 'Castelos, reinos e realeza',
    icon: 'crown',
    color: '#F472B6',
  },
  'super-heroi': {
    name: 'Super-Herói',
    description: 'Poderes especiais e missões heroicas',
    icon: 'shield',
    color: '#FBBF24',
  },
};

// Resposta completa do check-order-status
export interface OrderStatus_Response {
  order_id: number;
  status: OrderStatus;
  status_message: string;
  progress: number;        // 0–100
  book_file_url: string | null;
  created_at: string;      // "2026-02-17 10:30:00"
  updated_at: string;
  child_name: string;
  theme: ThemeKey;
}

// Resposta do create-order
export interface CreateOrderResponse {
  order_id: number;
  amount: number;          // em centavos
  product_type: ProductType;
  status: 'pending';
}

// Resposta do create-checkout-session
export interface CheckoutSessionResponse {
  session_id: string;      // Stripe session ID
  session_url: string;     // URL para redirecionar o usuário
}

// Resposta do upload-photo
export interface UploadPhotoResponse {
  filename: string;        // nome do arquivo (usar em create-order)
  path: string;            // caminho relativo
  url: string;             // URL pública
  size: number;            // bytes
  mime_type: string;
  dimensions: {
    width: number;
    height: number;
  };
}
```

---

### `src/types/wizard.ts` — Estado do wizard

```typescript
import type { ThemeKey, Gender, ProductType } from './order';

export interface WizardState {
  // Step 1
  theme: ThemeKey | null;

  // Step 2
  photo: {
    filename: string;  // enviado para create-order
    url: string;       // para preview
  } | null;
  childName: string;
  childAge: number | null;  // 1–12
  childGender: Gender;
  characteristics: string;  // máx 500 chars
  dedication: string;        // máx 300 chars
  productType: ProductType;

  // Step 3
  orderId: number | null;
}
```

---

## Regras de Negócio

### Validação de Campos (frontend)

#### Nome da criança
```typescript
const validateChildName = (name: string) => {
  if (!name.trim()) return 'Nome é obrigatório';
  if (name.length > 50) return 'Máximo 50 caracteres';
  return null;
};
```

#### Idade da criança
```typescript
// Faixa válida: 1 a 12 anos
// Exibir dropdown com valores 1–12 (não texto livre)
const MIN_CHILD_AGE = 1;
const MAX_CHILD_AGE = 12;
```

#### Características (opcional)
```typescript
const MAX_CHARACTERISTICS_LENGTH = 500;
```

#### Dedicatória (opcional)
```typescript
const MAX_DEDICATION_LENGTH = 300;
```

#### Senha
```typescript
const PASSWORD_MIN_LENGTH = 8;  // mínimo definido em config

// Força da senha (auth.js original)
const validatePasswordStrength = (password: string) => {
  let score = 0;
  const feedback: string[] = [];

  if (password.length >= 8) score++;
  else feedback.push('Mínimo 8 caracteres');

  if (/[A-Z]/.test(password)) score++;
  else feedback.push('Adicione letra maiúscula');

  if (/[a-z]/.test(password)) score++;
  else feedback.push('Adicione letra minúscula');

  if (/[0-9]/.test(password)) score++;
  else feedback.push('Adicione um número');

  if (/[^A-Za-z0-9]/.test(password)) score++;
  else feedback.push('Adicione caractere especial');

  const strength =
    score <= 1 ? 'fraca' :
    score <= 3 ? 'média' : 'forte';

  return { strength, score, feedback };
};
```

#### Upload de foto
```typescript
const UPLOAD_CONSTRAINTS = {
  maxSizeBytes: 5 * 1024 * 1024,  // 5MB
  allowedTypes: ['image/jpeg', 'image/png', 'image/webp'] as const,
  minDimensions: { width: 200, height: 200 },
};

// Validação client-side antes do upload:
const validateFile = (file: File): string | null => {
  if (!UPLOAD_CONSTRAINTS.allowedTypes.includes(file.type as any))
    return 'Tipo de arquivo não permitido. Use JPEG, PNG ou WebP.';
  if (file.size > UPLOAD_CONSTRAINTS.maxSizeBytes)
    return 'O arquivo é muito grande. Tamanho máximo: 5MB.';
  return null;  // dimensões são validadas pelo servidor
};
```

#### Telefone (máscara brasileira)
```typescript
// Formato esperado: (11) 99999-9999
const formatPhone = (value: string): string => {
  const digits = value.replace(/\D/g, '').slice(0, 11);
  if (digits.length <= 2) return `(${digits}`;
  if (digits.length <= 7) return `(${digits.slice(0,2)}) ${digits.slice(2)}`;
  if (digits.length <= 10) return `(${digits.slice(0,2)}) ${digits.slice(2,6)}-${digits.slice(6)}`;
  return `(${digits.slice(0,2)}) ${digits.slice(2,7)}-${digits.slice(7)}`;
};
```

---

### Preços

| Tipo | Centavos | Exibição |
|---|---|---|
| `ebook` | 2990 | R$ 29,90 |
| `physical` | 4990 | R$ 49,90 |
| `coloring_book` | 990 | R$ 9,90 |

> O preço **não é calculado no frontend**. O frontend envia `product_type` e o servidor determina o valor. Os preços acima são apenas para exibição.

```typescript
const formatPrice = (cents: number): string =>
  new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(cents / 100);
// Resultado: "R$ 29,90"
```

---

### Fluxo do Wizard — Guardas de Navegação

Cada step deve verificar que os dados dos steps anteriores existem:

```typescript
// Step 2: deve ter theme
// Se não tiver, redirecionar para /criar/tema
useEffect(() => {
  if (!wizardStore.theme) navigate('/criar/tema', { replace: true });
}, []);

// Step 3: deve ter theme + photo + dados da criança
// Se faltar, redirecionar para /criar/foto
useEffect(() => {
  if (!wizardStore.theme || !wizardStore.photo || !wizardStore.childName) {
    navigate('/criar/foto', { replace: true });
  }
}, []);

// Step 3 também recebe ?order_id= via query string
// Se não tiver, criar o pedido via createOrder
// Armazenar orderId no wizardStore após criar

// Step 4: deve ter orderId
useEffect(() => {
  if (!wizardStore.orderId) navigate('/criar/tema', { replace: true });
}, []);
```

---

### Polling de Status — Lógica

```typescript
// Regras de parada do polling:
const isTerminalStatus = (status: OrderStatus): boolean =>
  ['completed', 'failed', 'cancelled'].includes(status);

// Ao detectar status terminal:
// - completed → navegar para /criar/checkout?order_id=X e limpar wizard
// - failed → mostrar toast de erro, redirecionar para /minha-conta
// - cancelled → mostrar toast de aviso, redirecionar para /minha-conta

// Timeout: se após 10 minutos (600s) ainda não completou,
// exibir mensagem "Está demorando mais que o esperado. Você receberá um email."
// Mas NÃO tratar como erro — continuar polling.
```

---

### Autenticação — Regras

```typescript
// Roles e hierarquia:
// guest (0) < user (1) < admin (2) < super_admin (3)

// isAdmin: role === 'admin' || role === 'super_admin'
const isAdmin = (user: User | null): boolean =>
  user?.role === 'admin' || user?.role === 'super_admin';

// Rotas que requerem auth: /criar/*, /minha-conta
// Rotas que requerem admin: /admin/*
// Rotas públicas: /, /login, /register, /termos, /privacidade

// Comportamento ao não estar logado:
// - Tentar acessar /criar/* → redirecionar para /login?redirect=/criar/tema
// - Após login bem-sucedido → redirecionar para o redirect param

// Sessão: server-side via PHP session (cookie PHPSESSID)
// Duração: 7 dias (SESSION_LIFETIME)
// Não existe refresh token — sessão expira e o 401 da API avisa o frontend
```

---

### Status dos Pedidos — Regras de UI

```typescript
// Cores dos badges por status:
const STATUS_BADGE_VARIANT: Record<OrderStatus, 'success' | 'warning' | 'error' | 'info'> = {
  pending: 'warning',
  paid: 'info',
  generating: 'info',
  processing: 'info',
  completed: 'success',
  failed: 'error',
  refunded: 'warning',
  cancelled: 'error',
};

// Labels PT-BR:
const STATUS_LABELS: Record<OrderStatus, string> = {
  pending: 'Aguardando Pagamento',
  paid: 'Pagamento Confirmado',
  generating: 'Gerando Conteúdo',
  processing: 'Processando Livro',
  completed: 'Concluído',
  failed: 'Falhou',
  refunded: 'Reembolsado',
  cancelled: 'Cancelado',
};

// Ações disponíveis por status no Dashboard:
const STATUS_ACTIONS: Record<OrderStatus, 'download' | 'processing' | 'pay' | 'view' | null> = {
  completed: 'download',    // botão "Baixar PDF" com link direto
  processing: 'processing', // botão desabilitado "Em Produção..."
  generating: 'processing',
  paid: 'processing',
  pending: 'pay',           // botão "Finalizar Pagamento" → step 4
  failed: 'view',           // botão "Ver Detalhes"
  refunded: null,           // sem ação
  cancelled: null,          // sem ação
};
```

---

### Helpers de Data

```typescript
// Formato de exibição de datas: dd/mm/aaaa
const formatDate = (dateStr: string): string =>
  new Intl.DateTimeFormat('pt-BR').format(new Date(dateStr));
// "2026-02-17 10:30:00" → "17/02/2026"

const formatDateTime = (dateStr: string): string =>
  new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  }).format(new Date(dateStr));
// → "17/02/2026, 10:30"

const timeAgo = (dateStr: string): string => {
  const diff = Date.now() - new Date(dateStr).getTime();
  const rtf = new Intl.RelativeTimeFormat('pt-BR', { numeric: 'auto' });
  const minutes = Math.round(diff / 60000);
  const hours = Math.round(diff / 3600000);
  const days = Math.round(diff / 86400000);

  if (minutes < 60) return rtf.format(-minutes, 'minute');
  if (hours < 24) return rtf.format(-hours, 'hour');
  return rtf.format(-days, 'day');
};
// → "há 2 horas", "há 3 dias"
```

---

### Persistência do Wizard

```typescript
// O Zustand store do wizard deve usar sessionStorage como storage
// (os dados são limpos quando o usuário fecha o navegador)
// Comportamento equivalente ao sessionStorage atual do PHP

import { create } from 'zustand';
import { persist, createJSONStorage } from 'zustand/middleware';

const useWizardStore = create(
  persist(
    (set) => ({ /* estado e actions */ }),
    {
      name: 'wizard-state',
      storage: createJSONStorage(() => sessionStorage),
    }
  )
);

// Limpar o wizard após pedido concluído ou cancelado:
// wizardStore.reset() → chamado ao detectar status 'completed' e redirecionar
```

---

### Regras do Admin

O painel admin usa as mesmas APIs PHP mas endpoints específicos em `/api/admin/`.
O React deve verificar `isAdmin(user)` antes de renderizar qualquer rota `/admin/*`.

**Permissões por role:**
- `admin`: acesso a pedidos, leads, monitor IA
- `super_admin`: tudo + configurações do sistema

```typescript
// Proteção de rota admin no React:
const AdminRoute = ({ children }: { children: React.ReactNode }) => {
  const { user } = useAuthStore();
  if (!user) return <Navigate to="/admin/login" replace />;
  if (!isAdmin(user)) return <Navigate to="/" replace />;
  return <>{children}</>;
};
```

---

### IntersectionObserver (animações scroll)

Substituição do `observeElements()` do `main.js`:

```typescript
// Hook para animar elementos ao entrar no viewport
const useScrollAnimation = (ref: React.RefObject<Element>) => {
  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate-fadeIn');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1 }
    );

    if (ref.current) observer.observe(ref.current);
    return () => observer.disconnect();
  }, [ref]);
};

// Uso:
const sectionRef = useRef<HTMLElement>(null);
useScrollAnimation(sectionRef);
// <section ref={sectionRef} className="animate-on-scroll">
```

---

### Auto-refresh do Dashboard

O dashboard atual faz `setInterval(30000)` para recarregar pedidos em produção.

```typescript
// Com React Query:
const { data: orders } = useQuery({
  queryKey: ['user-orders'],
  queryFn: fetchUserOrders,
  refetchInterval: (data) => {
    const hasProcessing = data?.some(
      (o) => ['processing', 'generating', 'paid'].includes(o.status)
    );
    return hasProcessing ? 30000 : false;  // 30s ou parar
  },
});
```
