<?php
/**
 * Funções Utilitárias Gerais
 *
 * Funções helper para uso em toda a aplicação
 */

/**
 * Escapa HTML para prevenir XSS
 *
 * @param string $string String a escapar
 * @return string String escapada
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Redireciona para URL
 *
 * @param string $url URL de destino
 * @param int $statusCode Código HTTP
 */
function redirect($url, $statusCode = 302) {
    header('Location: ' . $url, true, $statusCode);
    exit;
}

/**
 * Retorna resposta JSON
 *
 * @param mixed $data Dados a retornar
 * @param int $statusCode Código HTTP
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Retorna resposta JSON de sucesso
 *
 * @param mixed $data Dados de resposta
 * @param string $message Mensagem de sucesso
 */
function jsonSuccess($data = null, $message = 'Operação realizada com sucesso') {
    $response = [
        'success' => true,
        'message' => $message
    ];

    if ($data !== null) {
        $response['data'] = $data;
    }

    jsonResponse($response);
}

/**
 * Retorna resposta JSON de erro
 *
 * @param string $message Mensagem de erro
 * @param int $statusCode Código HTTP
 * @param array $errors Detalhes dos erros
 */
function jsonError($message = 'Erro ao processar requisição', $statusCode = 400, $errors = []) {
    $response = [
        'success' => false,
        'message' => $message
    ];

    if (!empty($errors)) {
        $response['errors'] = $errors;
    }

    jsonResponse($response, $statusCode);
}

/**
 * Obtém input JSON do body da requisição
 *
 * @return array|null Dados JSON ou null
 */
function getJsonInput() {
    $json = file_get_contents('php://input');
    return json_decode($json, true);
}

/**
 * Valida email
 *
 * @param string $email Email a validar
 * @return bool True se válido
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida telefone brasileiro
 *
 * @param string $phone Telefone a validar
 * @return bool True se válido
 */
function isValidPhone($phone) {
    $cleaned = preg_replace('/\D/', '', $phone);
    return strlen($cleaned) >= 10 && strlen($cleaned) <= 11;
}

/**
 * Formata telefone brasileiro
 *
 * @param string $phone Telefone a formatar
 * @return string Telefone formatado
 */
function formatPhone($phone) {
    $cleaned = preg_replace('/\D/', '', $phone);

    if (strlen($cleaned) === 11) {
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $cleaned);
    } elseif (strlen($cleaned) === 10) {
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $cleaned);
    }

    return $phone;
}

/**
 * Formata preço em centavos para Real brasileiro
 *
 * @param int $cents Valor em centavos
 * @return string Valor formatado (ex: "R$ 29,90")
 */
function formatPrice($cents) {
    $reais = $cents / 100;
    return 'R$ ' . number_format($reais, 2, ',', '.');
}

/**
 * Formata data para padrão brasileiro
 *
 * @param string $date Data no formato Y-m-d ou timestamp
 * @return string Data formatada (ex: "10/01/2026")
 */
function formatDate($date) {
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date('d/m/Y', $timestamp);
}

/**
 * Formata data e hora para padrão brasileiro
 *
 * @param string $datetime Data/hora
 * @return string Data e hora formatadas
 */
function formatDateTime($datetime) {
    $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
    return date('d/m/Y H:i', $timestamp);
}

/**
 * Gera slug a partir de string
 *
 * @param string $string String a converter
 * @return string Slug gerado
 */
function slugify($string) {
    $string = mb_strtolower($string, 'UTF-8');

    // Remove acentos
    $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);

    // Remove caracteres especiais
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);

    // Substitui espaços e múltiplos hífens por um hífen
    $string = preg_replace('/[\s-]+/', '-', $string);

    // Remove hífens do início e fim
    $string = trim($string, '-');

    return $string;
}

/**
 * Gera hash de senha seguro
 *
 * @param string $password Senha em texto plano
 * @return string Hash da senha
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verifica se senha corresponde ao hash
 *
 * @param string $password Senha em texto plano
 * @param string $hash Hash armazenado
 * @return bool True se corresponde
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Gera string aleatória
 *
 * @param int $length Comprimento da string
 * @return string String aleatória
 */
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Trunca texto com reticências
 *
 * @param string $text Texto a truncar
 * @param int $length Comprimento máximo
 * @param string $suffix Sufixo (padrão: "...")
 * @return string Texto truncado
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Obtém extensão de arquivo
 *
 * @param string $filename Nome do arquivo
 * @return string Extensão em minúsculas
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Verifica se extensão de arquivo é permitida
 *
 * @param string $filename Nome do arquivo
 * @param array $allowedExtensions Extensões permitidas
 * @return bool True se permitida
 */
function isAllowedExtension($filename, $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp']) {
    $extension = getFileExtension($filename);
    return in_array($extension, $allowedExtensions);
}

/**
 * Limita tamanho de upload (em bytes)
 *
 * @param int $fileSize Tamanho do arquivo
 * @param int $maxSize Tamanho máximo (padrão: 5MB)
 * @return bool True se dentro do limite
 */
function isWithinSizeLimit($fileSize, $maxSize = 5242880) {
    return $fileSize <= $maxSize;
}

/**
 * Obtém URL base da aplicação
 *
 * @return string URL base
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . '://' . $host;
}

/**
 * Obtém URL completa atual
 *
 * @return string URL atual
 */
function getCurrentUrl() {
    return getBaseUrl() . $_SERVER['REQUEST_URI'];
}

/**
 * Sanitiza entrada de usuário
 *
 * @param string $input Input a sanitizar
 * @return string Input sanitizado
 */
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Converte array para XML
 *
 * @param array $data Dados a converter
 * @param string $rootElement Elemento raiz
 * @return string XML gerado
 */
function arrayToXml($data, $rootElement = 'root') {
    $xml = new SimpleXMLElement("<{$rootElement}/>");

    $arrayToXmlRecursive = function($data, $xml) use (&$arrayToXmlRecursive) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $subnode = $xml->addChild($key);
                $arrayToXmlRecursive($value, $subnode);
            } else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }
    };

    $arrayToXmlRecursive($data, $xml);

    return $xml->asXML();
}

/**
 * Debug helper - var_dump formatado
 *
 * @param mixed $var Variável a debugar
 * @param bool $die Se deve parar execução
 */
function dd($var, $die = true) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';

    if ($die) {
        die();
    }
}

/**
 * Tempo relativo (ex: "há 2 horas")
 *
 * @param string|int $datetime Data/hora ou timestamp
 * @return string Tempo relativo
 */
function timeAgo($datetime) {
    $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'agora mesmo';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return 'há ' . $mins . ' minuto' . ($mins > 1 ? 's' : '');
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return 'há ' . $hours . ' hora' . ($hours > 1 ? 's' : '');
    } elseif ($diff < 2592000) {
        $days = floor($diff / 86400);
        return 'há ' . $days . ' dia' . ($days > 1 ? 's' : '');
    } elseif ($diff < 31536000) {
        $months = floor($diff / 2592000);
        return 'há ' . $months . ' m' . ($months > 1 ? 'eses' : 'ês');
    } else {
        $years = floor($diff / 31536000);
        return 'há ' . $years . ' ano' . ($years > 1 ? 's' : '');
    }
}

/**
 * Obtém IP do cliente
 *
 * @return string IP do cliente
 */
function getClientIp() {
    $keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];

    foreach ($keys as $key) {
        if (isset($_SERVER[$key])) {
            $ip = explode(',', $_SERVER[$key])[0];
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }

    return '0.0.0.0';
}
