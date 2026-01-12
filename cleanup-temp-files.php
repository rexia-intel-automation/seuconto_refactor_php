<?php
/**
 * Script de Limpeza de Arquivos Temporários
 *
 * Remove arquivos de uploads/temp/ com mais de 24 horas
 * Deve ser executado via cronjob ou manualmente
 *
 * Uso:
 * - Via CLI: php cleanup-temp-files.php
 * - Via Cronjob: 0 3 * * * /usr/bin/php /caminho/para/cleanup-temp-files.php
 */

// Permitir execução apenas via CLI ou com chave secreta
if (php_sapi_name() !== 'cli') {
    // Se não for CLI, verificar chave secreta
    $secretKey = $_GET['key'] ?? '';
    $expectedKey = getenv('CLEANUP_SECRET_KEY') ?: 'change-me-in-production';

    if ($secretKey !== $expectedKey) {
        http_response_code(403);
        die('Acesso negado');
    }
}

require_once __DIR__ . '/config/config.php';

// Configurações
$tempDir = __DIR__ . '/uploads/temp/';
$maxAge = 24 * 60 * 60; // 24 horas em segundos
$now = time();

// Contadores
$totalFiles = 0;
$deletedFiles = 0;
$errors = 0;

echo "=== Iniciando Limpeza de Arquivos Temporários ===\n";
echo "Diretório: {$tempDir}\n";
echo "Idade máxima: " . ($maxAge / 3600) . " horas\n\n";

// Verificar se o diretório existe
if (!is_dir($tempDir)) {
    echo "ERRO: Diretório não encontrado: {$tempDir}\n";
    exit(1);
}

// Ler arquivos do diretório
$files = scandir($tempDir);

foreach ($files as $file) {
    // Ignorar . e ..
    if ($file === '.' || $file === '..' || $file === '.gitkeep') {
        continue;
    }

    $filePath = $tempDir . $file;

    // Verificar se é um arquivo
    if (!is_file($filePath)) {
        continue;
    }

    $totalFiles++;

    // Verificar idade do arquivo
    $fileAge = $now - filemtime($filePath);

    if ($fileAge > $maxAge) {
        // Arquivo antigo - deletar
        if (unlink($filePath)) {
            $deletedFiles++;
            $ageHours = round($fileAge / 3600, 1);
            echo "✓ Deletado: {$file} (idade: {$ageHours}h)\n";
        } else {
            $errors++;
            echo "✗ Erro ao deletar: {$file}\n";
        }
    }
}

echo "\n=== Resumo ===\n";
echo "Total de arquivos: {$totalFiles}\n";
echo "Arquivos deletados: {$deletedFiles}\n";
echo "Erros: {$errors}\n";

// Calcular espaço liberado (estimativa)
if ($deletedFiles > 0) {
    echo "\n✓ Limpeza concluída com sucesso!\n";
} else {
    echo "\nℹ Nenhum arquivo antigo encontrado.\n";
}

exit(0);
