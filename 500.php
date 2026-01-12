<?php
/**
 * Página de Erro 500 - Erro Interno do Servidor
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

http_response_code(500);

$pageTitle = 'Erro no Servidor - 500';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?> - Seu Conto</title>
    <link rel="stylesheet" href="<?php echo url('assets/css/main.css'); ?>">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .error-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 1rem;
        }

        .error-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .error-message {
            font-size: 1.125rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 2rem;
            border-radius: 9999px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .error-illustration {
            margin-bottom: 2rem;
            font-size: 6rem;
        }

        .error-details {
            margin-top: 2rem;
            padding: 1rem;
            background: #fef2f2;
            border-radius: 8px;
            border-left: 4px solid #ef4444;
        }

        .error-details-title {
            font-weight: 600;
            color: #991b1b;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .error-details-text {
            font-size: 0.875rem;
            color: #7f1d1d;
            margin: 0;
        }
    </style>
</head>
<body>

    <div class="error-container">
        <div class="error-card">
            <div class="error-illustration">⚠️</div>
            <div class="error-code">500</div>
            <h1 class="error-title">Erro no Servidor</h1>
            <p class="error-message">
                Desculpe! Algo deu errado do nosso lado.
                Nossa equipe já foi notificada e está trabalhando para resolver o problema.
                Por favor, tente novamente em alguns instantes.
            </p>

            <div class="error-actions">
                <a href="<?php echo url('/'); ?>" class="btn btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Voltar para Home
                </a>

                <button onclick="window.location.reload()" class="btn btn-secondary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
                    </svg>
                    Tentar Novamente
                </button>
            </div>

            <div class="error-details">
                <div class="error-details-title">O que você pode fazer?</div>
                <p class="error-details-text">
                    • Aguardar alguns minutos e tentar novamente<br>
                    • Limpar o cache do navegador<br>
                    • Contatar nosso suporte se o erro persistir
                </p>
            </div>
        </div>
    </div>

</body>
</html>
