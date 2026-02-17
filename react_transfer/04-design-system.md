# Design System — Seu Conto

Extraído integralmente de `assets/css/main.css`. Este é o sistema visual a ser reproduzido no React.

---

## Identidade Visual

**Estilo:** Bold/Neo-brutalist
- Bordas sólidas de 3px em quase todos os elementos
- Sombras **flat offset** (sem blur): `4px 4px 0 0 #cor`
- Hover: elemento sobe 2px e sombra cresce (efeito de elevação)
- Active: elemento desce 2px, sombra encolhe
- Cards aninhados: `section-card` envolve `inner-card`s
- Fontes arredondadas e amigáveis

---

## Tipografia

| Uso | Fonte | Fallback |
|---|---|---|
| Títulos (h1–h6) | Fredoka | sans-serif |
| Corpo / UI | Nunito | sans-serif |

**Import Google Fonts:**
```css
@import url('https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&family=Nunito:wght@300;400;600;700;800&display=swap');
```

**Escala tipográfica (Mobile First):**
| Tag | Base (<320px) | 320px+ | 640px+ | 1024px+ |
|---|---|---|---|---|
| h1 | 1.5rem | 1.75rem | 2.25rem | 2.75rem |
| h2 | 1.25rem | 1.5rem | 1.75rem | 2rem |
| h3 | 1.125rem | 1.25rem | 1.5rem | 1.75rem |
| h4 | 1rem | 1.125rem | 1.25rem | 1.5rem |
| body | 1rem | 1rem | 1rem | 1rem |

Line-height: `1.2` (títulos) / `1.6` (corpo)

---

## Paleta de Cores

Usa **OKLCH color space** (CSS moderno com melhor perceptual uniformity).

### Cores Primárias
| Token | Valor OKLCH | Hex aproximado | Uso |
|---|---|---|---|
| `--color-primary` | `oklch(0.70 0.15 280)` | `#7C6FCD` | CTA principal, destaques |
| `--color-primary-hover` | `oklch(0.65 0.18 280)` | `#6A5DC0` | Hover de botões primários |
| `--color-primary-light` | `oklch(0.85 0.08 280)` | `#C4BEE8` | Fundo de badges, focus ring |
| `--color-secondary` | `oklch(0.88 0.18 85)` | `#FFD166` | Botão CTA secundário (amarelo) |
| `--color-secondary-hover` | `oklch(0.83 0.20 85)` | `#FFC233` | Hover do CTA secundário |
| `--color-accent` | `oklch(0.95 0.02 280)` | `#F0EFF8` | Fundos suaves, hover states |

### Backgrounds e Superfícies
| Token | Valor OKLCH | Uso |
|---|---|---|
| `--color-background` | `oklch(0.98 0.01 85)` | Fundo da página (creme suave) |
| `--color-card` | `oklch(1 0 0)` | Branco puro — superfície de cards |
| `--color-muted` | `oklch(0.96 0.01 280)` | Progress bar fundo, skeleton |

### Textos
| Token | Valor OKLCH | Uso |
|---|---|---|
| `--color-foreground` | `oklch(0.25 0.02 280)` | Texto principal (quase preto roxo) |
| `--color-muted-foreground` | `oklch(0.50 0.01 280)` | Texto secundário, placeholders |

### Bordas
| Token | Valor | Uso |
|---|---|---|
| `--color-border` | `oklch(0.25 0.02 280)` | Bordas escuras (mesmo que foreground) |
| `--color-border-light` | `oklch(0.85 0.02 280)` | Bordas suaves, dividers |
| `--border-width` | `3px` | Espessura padrão de borda |
| `--border-style` | `solid` | Estilo padrão |

### Estados
| Token | Valor OKLCH | Hex aproximado | Uso |
|---|---|---|---|
| `--color-success` | `oklch(0.65 0.18 145)` | `#22C55E` | Sucesso, concluído |
| `--color-warning` | `oklch(0.75 0.18 85)` | `#F59E0B` | Aviso, pendente |
| `--color-error` | `oklch(0.60 0.22 30)` | `#EF4444` | Erro, falha |
| `--color-info` | `oklch(0.60 0.20 240)` | `#3B82F6` | Informação, processando |

### Cores dos Temas de Livro (hex exatos do config.php)
| Tema | Cor |
|---|---|
| aventura | `#FF6B6B` |
| fantasia | `#A78BFA` |
| espaco | `#60A5FA` |
| animais | `#34D399` |
| princesa | `#F472B6` |
| super-heroi | `#FBBF24` |

---

## Sombras

Sistema de sombras **flat offset** sem blur — característica central do design.

| Token | Valor | Uso |
|---|---|---|
| `--shadow-xs` | `2px 2px 0 0 var(--shadow-color)` | Elementos pequenos (badges) |
| `--shadow-sm` | `3px 3px 0 0 var(--shadow-color)` | Cards compactos |
| `--shadow` | `4px 4px 0 0 var(--shadow-color)` | Cards padrão |
| `--shadow-md` | `6px 6px 0 0 var(--shadow-color)` | Cards destacados |
| `--shadow-lg` | `8px 8px 0 0 var(--shadow-color)` | Section cards |
| `--shadow-xl` | `10px 10px 0 0 var(--shadow-color)` | Elementos hero |
| `--shadow-hover` | `6px 6px 0 0 var(--shadow-color)` | Estado hover (cards) |
| `--shadow-active` | `2px 2px 0 0 var(--shadow-color)` | Estado active (pressed) |
| `--shadow-primary` | `4px 4px 0 0 var(--color-primary)` | Seleção ativa (theme card) |
| `--shadow-color` | `oklch(0.25 0.02 280)` | Cor base das sombras |

**Hover pattern:**
```css
:hover {
  transform: translate(-2px, -2px); /* sobe à esquerda */
  box-shadow: var(--shadow-hover);  /* sombra cresce */
}
:active {
  transform: translate(2px, 2px);   /* desce à direita */
  box-shadow: var(--shadow-active); /* sombra encolhe */
}
```

---

## Border Radius

| Token | Valor | Uso |
|---|---|---|
| `--radius-xs` | `0.5rem` | — |
| `--radius-sm` | `0.75rem` | Badges, botões sm |
| `--radius` | `1rem` | Inputs, cards compactos |
| `--radius-md` | `1.25rem` | Inner cards |
| `--radius-lg` | `1.5rem` | Cards principais, botões lg |
| `--radius-xl` | `2rem` | Section cards mobile |
| `--radius-2xl` | `2.5rem` | Section cards tablet+ |
| `--radius-full` | `9999px` | Avatares, badges pill, step numbers |

---

## Espaçamento

| Token | Valor | Equivalente Tailwind |
|---|---|---|
| `--space-xs` | `0.25rem` | `p-1` |
| `--space-sm` | `0.5rem` | `p-2` |
| `--space-md` | `1rem` | `p-4` |
| `--space-lg` | `1.5rem` | `p-6` |
| `--space-xl` | `2rem` | `p-8` |
| `--space-2xl` | `3rem` | `p-12` |
| `--space-3xl` | `4rem` | `p-16` |
| `--space-4xl` | `6rem` | `p-24` |

Container max-width: `1280px`
Container padding: `0.75rem` (base) → `1rem` (320px+) → `1.5rem` (640px+) → `2rem` (1024px+)

---

## Breakpoints (Mobile First)

| Nome | min-width | Uso |
|---|---|---|
| xs | `< 320px` | Telefones muito pequenos (base) |
| sm | `320px` | Telefones pequenos |
| md | `480px` | Telefones grandes / phablets |
| lg | `640px` | Tablets / Landscape |
| xl | `768px` | Desktop (nav visível) |
| 2xl | `1024px` | Desktop largo |

---

## Grids

| Classe | Mobile (<480px) | 480px+ | 768px+ | 1024px+ |
|---|---|---|---|---|
| `.grid-2` | 1 col | 2 col | 2 col | 2 col |
| `.grid-3` | 1 col | 2 col | 2 col | 3 col |
| `.grid-4` | 1 col | 2 col | 3 col | 4 col |
| `.grid-auto` | 1 col | auto-fill 250px | auto-fill 280px | — |

---

## Componentes de UI

### Botões

**Base:** `border: 3px solid`, `font-weight: 700`, `border-radius: var(--radius)`, sombra flat, hover sobe/desce.

| Variante | Background | Cor texto | Borda |
|---|---|---|---|
| `.btn-primary` | `--color-primary` | branco | `--color-primary-hover` |
| `.btn-secondary` | `--color-secondary` | `--color-foreground` | `oklch(0.70 0.18 85)` |
| `.btn-outline` | `--color-card` | `--color-primary` | `--color-primary` |
| `.btn-ghost` | transparente | `--color-foreground` | transparente, sem sombra |

**Tamanhos:**
- `.btn-sm`: padding `0.5rem 1rem`, font 0.875rem
- `.btn` (default): padding `1rem 1.5rem` (mobile+), font 1rem
- `.btn-lg`: padding `1.5rem 2rem`, font 1.125rem (mobile+) / 1.25rem (640px+)
- `.btn-full`: width 100%

**Disabled:** opacity 0.5, cursor not-allowed, sem transform/sombra extra.

---

### Cards

**`.card`** — Card padrão com hover
- background: card, border: 3px solid, border-radius: `--radius-lg`, shadow: `--shadow`
- hover: `translate(-2px, -2px)` + shadow-hover

**`.inner-card`** — Card dentro de section-card
- border: 2px solid, border-radius: `--radius-md`, shadow: `--shadow-sm`

**`.section-card`** — Wrapper de seção inteira
- Padding generoso, border-radius grande (`--radius-2xl` no desktop)
- shadow: `--shadow-lg`

**Variantes de section-card:**
- `.section-card--accent`: background `--color-accent`
- `.section-card--gradient`: `linear-gradient(135deg, --color-primary 0%, oklch(0.60 0.20 280) 100%)` — texto branco

---

### Badges

- border: 2px solid, border-radius: full (pill)
- Variantes: `.badge-primary`, `.badge-success`, `.badge-warning`, `.badge-error`, `.badge-info`

---

### Inputs / Forms

```css
/* Todos os inputs seguem o padrão: */
border: 3px solid var(--color-border);
border-radius: var(--radius);
box-shadow: var(--shadow-sm);
font-family: Nunito;
font-size: 1rem;
padding: 1rem;

/* Focus: */
border-color: var(--color-primary);
box-shadow: var(--shadow), 0 0 0 3px var(--color-primary-light);
```

---

### Upload Area

```css
border: 3px dashed var(--color-border);  /* borda tracejada */
border-radius: var(--radius-lg);
/* hover: border solid primary, background accent */
/* dragover: border primary + shadow focus ring */
```

---

### Progress Bar

- altura: 12px
- background: `--color-muted`
- border: 2px solid `--color-border`
- border-radius: full
- fill: `--color-primary`, transition 300ms

**Step dots:**
- default: background card, border 3px
- active: background `--color-primary`, cor branca
- completed: background `--color-success`, cor branca

---

### Theme Cards (no wizard Step 1)

```css
border: 3px solid var(--color-border);
border-radius: var(--radius-lg);
box-shadow: var(--shadow);
cursor: pointer;

/* hover: */
transform: translate(-4px, -4px);  /* mais dramático que cards normais */
box-shadow: var(--shadow-lg);

/* .selected: */
border-color: var(--color-primary);
box-shadow: var(--shadow-primary);  /* sombra roxa */
```

---

### Toasts

- Posição: fixed, bottom + right (desktop) / bottom + esquerda+direita (mobile)
- z-index: 9999
- border-radius: `--radius-lg`
- box-shadow: `--shadow-lg`
- border-left: 6px solid (cor por tipo)
- Animação entrada: `slideInRight 0.3s ease-out`
- Tipos: success (verde), error (vermelho), warning (amarelo), info (azul)

---

### Animações Disponíveis

| Classe / Keyframe | Comportamento |
|---|---|
| `.animate-fadeIn` | opacity 0→1, translateY 20px→0, 0.5s |
| `.animate-fadeInUp` | opacity 0→1, translateY 30px→0, 0.6s |
| `.animate-pulse` | opacity 1→0.7→1, 2s infinito |
| `.animate-bounce` | translateY 0→-10px→0, 2s infinito |
| `.animate-on-scroll` | opacity 0, translateY 30px inicial; recebe `.animate-fadeIn` pelo IntersectionObserver |
| `shimmer` | gradient skeleton loading |
| `spin` | rotação contínua (spinner) |

---

### Spinner

```css
border: 3px solid rgba(255,255,255, 0.3);
border-top-color: currentColor;
animation: spin 0.8s linear infinite;
```

Variante `.spinner--dark`:
```css
border-color: var(--color-border-light);
border-top-color: var(--color-primary);
```

---

### Gradientes

```css
.gradient-text {
  background: linear-gradient(135deg, var(--color-primary) 0%, oklch(0.55 0.20 280) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
```

---

### Skeleton Loading

Shimmer animado sobre gradiente `var(--color-muted)` → `var(--color-accent)`.
Classes: `.skeleton-text`, `.skeleton-title`, `.skeleton-image`, `.skeleton-avatar`, `.skeleton-card`.

---

## Equivalências Tailwind

Para implementar o design system em Tailwind, configurar em `tailwind.config.ts`:

```typescript
import type { Config } from 'tailwindcss';

const config: Config = {
  content: ['./index.html', './src/**/*.{ts,tsx}'],
  theme: {
    extend: {
      fontFamily: {
        heading: ['Fredoka', 'sans-serif'],
        body: ['Nunito', 'sans-serif'],
      },
      colors: {
        primary: 'oklch(0.70 0.15 280)',
        'primary-hover': 'oklch(0.65 0.18 280)',
        'primary-light': 'oklch(0.85 0.08 280)',
        secondary: 'oklch(0.88 0.18 85)',
        accent: 'oklch(0.95 0.02 280)',
        background: 'oklch(0.98 0.01 85)',
        card: 'oklch(1 0 0)',
        muted: 'oklch(0.96 0.01 280)',
        foreground: 'oklch(0.25 0.02 280)',
        'muted-foreground': 'oklch(0.50 0.01 280)',
        border: 'oklch(0.25 0.02 280)',
        'border-light': 'oklch(0.85 0.02 280)',
        success: 'oklch(0.65 0.18 145)',
        warning: 'oklch(0.75 0.18 85)',
        error: 'oklch(0.60 0.22 30)',
        info: 'oklch(0.60 0.20 240)',
      },
      borderWidth: {
        DEFAULT: '3px',
        '2': '2px',
        '4': '4px',
      },
      borderRadius: {
        sm: '0.75rem',
        DEFAULT: '1rem',
        md: '1.25rem',
        lg: '1.5rem',
        xl: '2rem',
        '2xl': '2.5rem',
      },
      boxShadow: {
        xs: '2px 2px 0 0 oklch(0.25 0.02 280)',
        sm: '3px 3px 0 0 oklch(0.25 0.02 280)',
        DEFAULT: '4px 4px 0 0 oklch(0.25 0.02 280)',
        md: '6px 6px 0 0 oklch(0.25 0.02 280)',
        lg: '8px 8px 0 0 oklch(0.25 0.02 280)',
        xl: '10px 10px 0 0 oklch(0.25 0.02 280)',
        hover: '6px 6px 0 0 oklch(0.25 0.02 280)',
        active: '2px 2px 0 0 oklch(0.25 0.02 280)',
        primary: '4px 4px 0 0 oklch(0.70 0.15 280)',
      },
      screens: {
        xs: '320px',
        sm: '480px',
        md: '640px',
        lg: '768px',
        xl: '1024px',
        '2xl': '1280px',
      },
      spacing: {
        xs: '0.25rem',
        sm: '0.5rem',
        md: '1rem',
        lg: '1.5rem',
        xl: '2rem',
        '2xl': '3rem',
        '3xl': '4rem',
        '4xl': '6rem',
      },
      maxWidth: {
        container: '1280px',
      },
    },
  },
  plugins: [],
};

export default config;
```

> **Nota:** OKLCH pode não ser suportado nativamente pelo Tailwind v3. Nesse caso, usar os valores hex aproximados ou migrar para Tailwind v4 que suporta OKLCH nativamente.
