<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/stripe.php';

$input = getJsonInput();
$action = $input['action'] ?? '';

try {
    switch ($action) {
        case 'createCheckoutSession':
            $db = getDB();
            
            // Valida dados
            $required = ['customerName', 'customerEmail', 'childName', 'childAge', 'childGender', 'theme'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    throw new Exception("Campo {$field} é obrigatório");
                }
            }

            // Cria pedido no banco
            $stmt = $db->prepare("
                INSERT INTO orders (
                    user_id, customer_name, customer_email, customer_phone,
                    child_name, child_age, child_gender, child_characteristics,
                    theme, dedication, includes_coloring_book,
                    base_price, coloring_book_price, total_price,
                    delivery_method, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");

            $userId = isLoggedIn() ? getCurrentUser()['id'] : null;
            $includesColoring = $input['includesColoringBook'] ?? false;
            $basePrice = PRICE_EBOOK;
            $coloringPrice = $includesColoring ? PRICE_COLORING_BOOK : 0;
            $totalPrice = $basePrice + $coloringPrice;

            $stmt->execute([
                $userId,
                $input['customerName'],
                $input['customerEmail'],
                $input['customerPhone'] ?? '',
                $input['childName'],
                $input['childAge'],
                $input['childGender'],
                $input['childCharacteristics'] ?? '',
                $input['theme'],
                $input['dedication'] ?? '',
                $includesColoring ? 1 : 0,
                $basePrice,
                $coloringPrice,
                $totalPrice,
                $input['deliveryMethod'] ?? 'email'
            ]);

            $orderId = $db->lastInsertId();

            // Cria sessão Stripe
            $lineItems = [
                [
                    'price_data' => [
                        'currency' => 'brl',
                        'product_data' => [
                            'name' => 'E-book Personalizado - ' . ucfirst($input['theme']),
                            'description' => 'História personalizada para ' . $input['childName'],
                        ],
                        'unit_amount' => $basePrice,
                    ],
                    'quantity' => 1,
                ]
            ];

            if ($includesColoring) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'brl',
                        'product_data' => [
                            'name' => 'Livro de Colorir',
                            'description' => 'Versão para colorir',
                        ],
                        'unit_amount' => $coloringPrice,
                    ],
                    'quantity' => 1,
                ];
            }

            $session = StripeConfig::createCheckoutSession([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => getBaseUrl() . '/refactor/pages/sucesso.php?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => getBaseUrl() . '/refactor/pages/checkout.php',
                'client_reference_id' => $orderId,
                'customer_email' => $input['customerEmail'],
            ]);

            // Salva session_id no pedido
            $stmt = $db->prepare("UPDATE orders SET stripe_checkout_session_id = ? WHERE id = ?");
            $stmt->execute([$session->id, $orderId]);

            jsonSuccess([
                'orderId' => $orderId,
                'checkoutUrl' => $session->url,
                'sessionId' => $session->id
            ]);
            break;

        default:
            throw new Exception('Ação inválida');
    }
} catch (Exception $e) {
    jsonError($e->getMessage());
}
