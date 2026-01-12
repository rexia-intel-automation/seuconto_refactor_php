<?php
/**
 * Landing Page - Seu Conto
 *
 * Página inicial com apresentação do produto e CTAs
 */

require_once __DIR__ . '/config/bootstrap.php';

$pageTitle = 'Seu Conto - Livros Infantis Personalizados com IA';
$pageDescription = 'Crie livros infantis personalizados com IA. Transforme fotos reais em ilustrações mágicas. Entregue em até 30 minutos!';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<?php include __DIR__ . '/components/head.php'; ?>
</head>
<body>

    <?php include __DIR__ . '/components/header.php'; ?>

    <main>
        <?php include __DIR__ . '/components/landing/hero.php'; ?>

        <?php include __DIR__ . '/components/landing/how-it-works.php'; ?>

        <?php include __DIR__ . '/components/landing/themes.php'; ?>

        <?php include __DIR__ . '/components/landing/testimonials.php'; ?>

        <?php include __DIR__ . '/components/landing/faq.php'; ?>

        <?php include __DIR__ . '/components/landing/cta-final.php'; ?>
    </main>

    <?php include __DIR__ . '/components/footer.php'; ?>
