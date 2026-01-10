<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

$input = getJsonInput();
$action = $input['action'] ?? '';

try {
    switch ($action) {
        case 'register':
            $fullName = sanitizeInput($input['fullName'] ?? '');
            $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
            $phone = sanitizeInput($input['phone'] ?? '');
            $password = $input['password'] ?? '';

            if (!$fullName || !$email || !$phone || strlen($password) < 6) {
                throw new Exception('Dados inválidos');
            }

            $db = getDB();
            
            // Verifica se email já existe
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new Exception('Email já cadastrado');
            }

            // Cria usuário
            $passwordHash = hashPassword($password);
            $stmt = $db->prepare("INSERT INTO users (full_name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
            $stmt->execute([$fullName, $email, $phone, $passwordHash]);
            
            $userId = $db->lastInsertId();
            
            // Define sessão
            setUserSession([
                'id' => $userId,
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'role' => 'user'
            ]);

            jsonSuccess(['userId' => $userId], 'Conta criada com sucesso!');
            break;

        case 'login':
            $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
            $password = $input['password'] ?? '';

            if (!$email || !$password) {
                throw new Exception('Email e senha são obrigatórios');
            }

            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user || !verifyPassword($password, $user['password_hash'])) {
                throw new Exception('Email ou senha incorretos');
            }

            // Atualiza último login
            $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);

            setUserSession($user);
            jsonSuccess(['userId' => $user['id']], 'Login realizado com sucesso!');
            break;

        case 'logout':
            clearUserSession();
            jsonSuccess(null, 'Logout realizado com sucesso');
            break;

        default:
            throw new Exception('Ação inválida');
    }
} catch (Exception $e) {
    jsonError($e->getMessage());
}
