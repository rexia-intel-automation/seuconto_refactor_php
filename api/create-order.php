<?php
/**
 * API: Criar Pedido
 *
 * Cria um pedido no banco de dados com os dados do usuário
 * Não dispara n8n ainda - isso só acontece após pagamento confirmado
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../services/OrderService.php';

// Verificar autenticação
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Usuário não autenticado'
    ]);
    exit;
}

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Método não permitido. Use POST.'
    ]);
    exit;
}

// Obter dados JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Dados inválidos'
    ]);
    exit;
}

// Validar campos obrigatórios
$required = ['theme', 'child_name', 'child_age', 'photo_file'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => "Campo obrigatório ausente: {$field}"
        ]);
        exit;
    }
}

// Validar tema
$validThemes = array_keys(AVAILABLE_THEMES);
if (!in_array($data['theme'], $validThemes)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Tema inválido'
    ]);
    exit;
}

// Validar idade
$age = intval($data['child_age']);
if ($age < 1 || $age > 12) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Idade deve estar entre 1 e 12 anos'
    ]);
    exit;
}

// Obter usuário atual
$user = getCurrentUser();

// Determinar tipo de produto (por padrão, ebook)
$productType = $data['product_type'] ?? 'ebook';
$amount = $productType === 'ebook' ? PRICE_EBOOK : PRICE_PHYSICAL;

try {
    // Criar pedido usando OrderService
    // NOTA: OrderService espera 'price' e 'photo_path', não 'amount' e 'child_photo_url'
    $orderData = [
        'user_id' => $user['id'],
        'customer_email' => $user['email'],
        'customer_name' => $user['name'],
        'child_name' => trim($data['child_name']),
        'child_age' => $age,
        'theme' => $data['theme'],
        'price' => $amount, // OrderService espera 'price'
        'photo_path' => 'uploads/temp/' . $data['photo_file'], // OrderService espera 'photo_path'
        'status' => 'pending' // Aguardando pagamento
    ];

    $order = OrderService::createOrder($orderData);
    $orderId = $order['id'];

    // Log de sucesso
    error_log("Pedido #{$orderId} criado por usuário {$user['id']}");

    // Retornar sucesso
    echo json_encode([
        'success' => true,
        'message' => 'Pedido criado com sucesso!',
        'data' => [
            'order_id' => $orderId,
            'amount' => $amount,
            'product_type' => $productType,
            'status' => 'pending'
        ]
    ]);

} catch (Exception $e) {
    error_log("Erro ao criar pedido: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao criar pedido. Tente novamente.'
    ]);
}
