<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $pageDescription ?? 'Transforme sua criança no protagonista de uma história mágica personalizada com inteligência artificial'; ?>">
    <meta name="keywords" content="<?php echo $pageKeywords ?? 'livros personalizados, histórias infantis, IA, presente criativo, e-book infantil'; ?>">
    <meta name="author" content="Seu Conto">

    <title><?php echo $pageTitle ?? 'Seu Conto - Livros Infantis Personalizados com IA'; ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo asset('images/favicon.svg'); ?>">

    <!-- Preconnect para fontes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- CSS Principal -->
    <link rel="stylesheet" href="<?php echo asset('css/main.css'); ?>">

    <!-- CSS Adicional (definido pela página) -->
    <?php if (isset($additionalCSS) && is_array($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo BASE_URL . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:title" content="<?php echo $pageTitle ?? 'Seu Conto - Livros Infantis Personalizados'; ?>">
    <meta property="og:description" content="<?php echo $pageDescription ?? 'Histórias mágicas personalizadas criadas com IA'; ?>">
    <meta property="og:image" content="<?php echo BASE_URL . '/assets/img/og-image.jpg'; ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo BASE_URL . $_SERVER['REQUEST_URI']; ?>">
    <meta property="twitter:title" content="<?php echo $pageTitle ?? 'Seu Conto - Livros Infantis Personalizados'; ?>">
    <meta property="twitter:description" content="<?php echo $pageDescription ?? 'Histórias mágicas personalizadas criadas com IA'; ?>">
    <meta property="twitter:image" content="<?php echo BASE_URL . '/assets/img/og-image.jpg'; ?>">
</head>
<body>
