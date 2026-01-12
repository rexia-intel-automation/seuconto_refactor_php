<?php
/**
 * API de Upload de Fotos
 *
 * Endpoint para upload de fotos das crianças
 * Valida, salva em uploads/temp/ e retorna o caminho
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Verificar se o usuário está autenticado
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Você precisa estar logado para fazer upload de fotos'
    ]);
    exit;
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Método não permitido. Use POST.'
    ]);
    exit;
}

// Verificar se o arquivo foi enviado
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Nenhum arquivo foi enviado'
    ]);
    exit;
}

$file = $_FILES['photo'];

// Verificar erros de upload
if ($file['error'] !== UPLOAD_ERR_OK) {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'O arquivo é muito grande (limite do servidor)',
        UPLOAD_ERR_FORM_SIZE => 'O arquivo é muito grande (limite do formulário)',
        UPLOAD_ERR_PARTIAL => 'O arquivo foi parcialmente enviado',
        UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária não encontrada',
        UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever arquivo no disco',
        UPLOAD_ERR_EXTENSION => 'Upload bloqueado por extensão'
    ];

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $errorMessages[$file['error']] ?? 'Erro desconhecido no upload'
    ]);
    exit;
}

// Validar tipo de arquivo
$allowedMimeTypes = [
    'image/jpeg',
    'image/jpg',
    'image/png',
    'image/webp'
];

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedMimeTypes)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Tipo de arquivo não permitido. Use JPEG, PNG ou WebP.'
    ]);
    exit;
}

// Validar tamanho do arquivo (máximo 5MB)
$maxFileSize = 5 * 1024 * 1024; // 5MB em bytes

if ($file['size'] > $maxFileSize) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'O arquivo é muito grande. Tamanho máximo: 5MB.'
    ]);
    exit;
}

// Validar dimensões da imagem (opcional, mas recomendado)
$imageInfo = getimagesize($file['tmp_name']);
if ($imageInfo === false) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Arquivo não é uma imagem válida'
    ]);
    exit;
}

$width = $imageInfo[0];
$height = $imageInfo[1];

// Validar dimensões mínimas (200x200 pixels)
if ($width < 200 || $height < 200) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'A imagem é muito pequena. Tamanho mínimo: 200x200 pixels.'
    ]);
    exit;
}

// Gerar nome único para o arquivo
$extension = match($mimeType) {
    'image/jpeg', 'image/jpg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    default => 'jpg'
};

$user = getCurrentUser();
$timestamp = time();
$randomString = bin2hex(random_bytes(8));
$fileName = "photo_{$user['id']}_{$timestamp}_{$randomString}.{$extension}";

// Definir caminho de destino
$uploadDir = __DIR__ . '/../uploads/temp/';
$uploadPath = $uploadDir . $fileName;

// Criar diretório se não existir
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Erro ao criar diretório de upload'
        ]);
        exit;
    }
}

// Mover arquivo para o destino
if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao salvar o arquivo'
    ]);
    exit;
}

// Aplicar permissões adequadas
chmod($uploadPath, 0644);

// Calcular URL pública do arquivo
$fileUrl = url('uploads/temp/' . $fileName);

// Log de sucesso
error_log("Upload realizado com sucesso: {$fileName} por usuário {$user['id']}");

// Retornar sucesso
echo json_encode([
    'success' => true,
    'message' => 'Foto enviada com sucesso!',
    'data' => [
        'filename' => $fileName,
        'path' => 'uploads/temp/' . $fileName,
        'url' => $fileUrl,
        'size' => $file['size'],
        'mime_type' => $mimeType,
        'dimensions' => [
            'width' => $width,
            'height' => $height
        ]
    ]
]);
