# 📦 Componentes - Seu Conto

Diretório de componentes reutilizáveis do projeto.

## 📂 Estrutura

```
components/
├── head.php           # Meta tags, CSS, SEO
├── header.php         # Cabeçalho e navegação do site
├── footer.php         # Rodapé do site
├── landing/           # Componentes da landing page
│   ├── hero.php
│   ├── transformation.php
│   ├── how-it-works.php
│   ├── inside-book.php
│   └── social-impact.php
└── admin/             # Componentes do painel admin
    ├── sidebar.php
    ├── topbar.php
    └── kpi-card.php
```

## 🔄 Como Usar

### Páginas Públicas (Landing, Dashboard de Usuário)

```php
<?php
// Configurações da página
$pageTitle = 'Título da Página';
$pageDescription = 'Descrição SEO';
$additionalCSS = ['/refactor/assets/css/custom.css'];
$additionalJS = ['/refactor/assets/js/custom.js'];

// Inclui dependências
require_once __DIR__ . '/config/paths.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Obtém dados do usuário
$currentUser = getCurrentUser();
$isLoggedIn = isLoggedIn();

// Inclui head + header
require_once __DIR__ . '/components/head.php';
require_once __DIR__ . '/components/header.php';
?>

<!-- Conteúdo da página aqui -->
<div class="container">
    <h1>Meu Conteúdo</h1>
</div>

<?php require_once __DIR__ . '/components/footer.php'; ?>
```

### Landing Page com Componentes Modulares

```php
<?php
// ... includes ...
require_once __DIR__ . '/components/head.php';
require_once __DIR__ . '/components/header.php';
?>

<?php require_once __DIR__ . '/components/landing/hero.php'; ?>
<?php require_once __DIR__ . '/components/landing/transformation.php'; ?>
<?php require_once __DIR__ . '/components/landing/how-it-works.php'; ?>
<?php require_once __DIR__ . '/components/landing/inside-book.php'; ?>
<?php require_once __DIR__ . '/components/landing/social-impact.php'; ?>

<?php require_once __DIR__ . '/components/footer.php'; ?>
```

### Páginas Admin

```php
<?php
// Configurações
$pageTitle = 'Dashboard Admin';
$pageSubtitle = 'Visão geral do sistema';

// Inclui dependências
require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/permissions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/admin-middleware.php';

// Protege rota
requireAdmin();
$currentUser = getCurrentUser();

// Inclui head (sem header normal)
require_once __DIR__ . '/../../components/head.php';
?>

<!-- Layout Admin -->
<div style="display: flex;">
    <?php require_once __DIR__ . '/../../components/admin/sidebar.php'; ?>

    <div style="flex: 1; margin-left: 260px;">
        <?php require_once __DIR__ . '/../../components/admin/topbar.php'; ?>

        <main style="padding: 2rem;">
            <!-- Conteúdo Admin -->

            <!-- Exemplo de KPI Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <?php
                $kpiData = [
                    'title' => 'Total de Pedidos',
                    'value' => '150',
                    'change' => '+12.5%',
                    'trend' => 'up',
                    'icon' => '<svg width="24" height="24">...</svg>',
                    'bgColor' => 'var(--color-primary)'
                ];
                require __DIR__ . '/../../components/admin/kpi-card.php';
                ?>
            </div>
        </main>
    </div>
</div>

</body>
</html>
```

## ⚙️ Variáveis Disponíveis

### Para `head.php`

| Variável | Tipo | Descrição | Padrão |
|----------|------|-----------|---------|
| `$pageTitle` | string | Título da página | 'Seu Conto - Livros Infantis...' |
| `$pageDescription` | string | Meta description | Descrição padrão |
| `$pageKeywords` | string | Meta keywords | Keywords padrão |
| `$additionalCSS` | array | Arquivos CSS extras | `[]` |

### Para `header.php`

| Variável | Tipo | Descrição |
|----------|------|-----------|
| `$isLoggedIn` | boolean | Se usuário está autenticado |
| `$currentUser` | array | Dados do usuário logado |

### Para `admin/kpi-card.php`

| Variável | Tipo | Descrição | Obrigatório |
|----------|------|-----------|-------------|
| `$kpiData['title']` | string | Título do KPI | ✅ |
| `$kpiData['value']` | string | Valor principal | ✅ |
| `$kpiData['change']` | string | Variação (ex: "+12.5%") | ❌ |
| `$kpiData['trend']` | string | 'up', 'down' ou 'neutral' | ❌ |
| `$kpiData['icon']` | string | SVG do ícone | ❌ |
| `$kpiData['bgColor']` | string | Cor de fundo do ícone | ❌ |

## 📋 Migração de Arquivos Antigos

Arquivos que precisam ser atualizados para usar os novos components:

```bash
# Buscar arquivos que ainda usam includes/header.php
grep -r "includes/header.php" pages/

# Arquivos encontrados:
# - pages/dashboard.php
# - pages/auth/login.php
# - pages/auth/register.php
# - pages/checkout.php
# - pages/criar.php
```

### Script de Migração

Substituir:
```php
require_once __DIR__ . '/../includes/header.php';
```

Por:
```php
require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$currentUser = getCurrentUser();
$isLoggedIn = isLoggedIn();

require_once __DIR__ . '/../components/head.php';
require_once __DIR__ . '/../components/header.php';
```

E substituir:
```php
require_once __DIR__ . '/../includes/footer.php';
```

Por:
```php
require_once __DIR__ . '/../components/footer.php';
```

## 🎨 Componentes da Landing Page

### `hero.php`
Seção principal com título, subtítulo, CTA e estatísticas.

### `transformation.php`
Exemplos antes/depois de fotos transformadas em ilustrações.

### `how-it-works.php`
3 passos explicando o funcionamento do serviço.

### `inside-book.php`
Features do que vem dentro de cada livro.

### `social-impact.php`
Banner de impacto social (doação de livros).

## 🔐 Componentes Admin

### `sidebar.php`
Menu lateral fixo com navegação do painel admin.

Requer:
- `$currentUser['name']` para exibir avatar

### `topbar.php`
Barra superior com título da página, notificações e menu do usuário.

Requer:
- `$pageTitle` para exibir título
- `$pageSubtitle` (opcional)
- `$currentUser['name']` para menu

### `kpi-card.php`
Card de métrica com valor, variação e ícone.

Uso:
```php
$kpiData = [
    'title' => 'Receita Mensal',
    'value' => 'R$ 15.240',
    'change' => '+18.2%',
    'trend' => 'up'
];
require 'components/admin/kpi-card.php';
```

## 📝 Notas

1. **Sempre defina as variáveis antes de incluir os components**
2. **Use caminhos relativos corretos baseados na localização do arquivo**
3. **Componentes admin não incluem `footer.php`** (layout diferente)
4. **Flash messages são exibidos automaticamente no `header.php`**

## 🚀 Próximos Passos

- [ ] Migrar todos os arquivos em `pages/` para usar novos components
- [ ] Criar mais componentes da landing (roadmap, FAQ, testimonials)
- [ ] Criar componentes de charts para admin
- [ ] Adicionar dark mode toggle nos components
