<?php
/**
 * Termos de Uso
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

$pageTitle = 'Termos de Uso - Seu Conto';
$pageDescription = 'Termos e condições de uso da plataforma Seu Conto';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php include __DIR__ . '/../../components/head.php'; ?>
</head>
<body>

    <?php include __DIR__ . '/../../components/header.php'; ?>

    <main class="legal-page">
        <div class="container" style="max-width: 900px; padding: 4rem 2rem;">

            <!-- Header -->
            <div style="text-align: center; margin-bottom: 3rem;">
                <h1 style="font-size: 2.5rem; margin-bottom: 1rem; color: #111827;">Termos de Uso</h1>
                <p class="text-muted">Última atualização: <?php echo date('d/m/Y'); ?></p>
            </div>

            <!-- Conteúdo -->
            <div class="legal-content" style="line-height: 1.8; color: #374151;">

                <section style="margin-bottom: 3rem;">
                    <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: #1f2937;">1. Aceitação dos Termos</h2>
                    <p style="margin-bottom: 1rem;">
                        <!-- Conteúdo a ser preenchido -->
                    </p>
                </section>

                <section style="margin-bottom: 3rem;">
                    <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: #1f2937;">2. Uso da Plataforma</h2>
                    <p style="margin-bottom: 1rem;">
                        <!-- Conteúdo a ser preenchido -->
                    </p>
                </section>

                <section style="margin-bottom: 3rem;">
                    <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: #1f2937;">3. Direitos de Propriedade Intelectual</h2>
                    <p style="margin-bottom: 1rem;">
                        <!-- Conteúdo a ser preenchido -->
                    </p>
                </section>

                <section style="margin-bottom: 3rem;">
                    <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: #1f2937;">4. Uso de Imagens e Conteúdo</h2>
                    <p style="margin-bottom: 1rem;">
                        <!-- Conteúdo a ser preenchido -->
                    </p>
                </section>

                <section style="margin-bottom: 3rem;">
                    <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: #1f2937;">5. Pagamentos e Reembolsos</h2>
                    <p style="margin-bottom: 1rem;">
                        <!-- Conteúdo a ser preenchido -->
                    </p>
                </section>

                <section style="margin-bottom: 3rem;">
                    <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: #1f2937;">6. Limitação de Responsabilidade</h2>
                    <p style="margin-bottom: 1rem;">
                        <!-- Conteúdo a ser preenchido -->
                    </p>
                </section>

                <section style="margin-bottom: 3rem;">
                    <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: #1f2937;">7. Modificações nos Termos</h2>
                    <p style="margin-bottom: 1rem;">
                        <!-- Conteúdo a ser preenchido -->
                    </p>
                </section>

                <section style="margin-bottom: 3rem;">
                    <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: #1f2937;">8. Contato</h2>
                    <p style="margin-bottom: 1rem;">
                        <!-- Conteúdo a ser preenchido -->
                    </p>
                </section>

            </div>

            <!-- Voltar -->
            <div style="text-align: center; margin-top: 3rem;">
                <a href="<?php echo url('/'); ?>" class="btn btn-outline">Voltar para Home</a>
            </div>

        </div>
    </main>

    <?php include __DIR__ . '/../../components/footer.php'; ?>

</body>
</html>
