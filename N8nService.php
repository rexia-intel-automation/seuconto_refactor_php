// Exemplo simplificado de services/N8nService.php
class N8nService {
    public static function triggerWorkflow($clientName, $photoPath, $theme) {
        $env = require __DIR__ . '/../config/env.php';
        $url = $env['N8N_WEBHOOK_URL'];

        // Prepara payload (Multipart se enviar arquivo, ou JSON se enviar URL)
        $data = [
            'name' => $clientName,
            'theme' => $theme,
            'photo' => new CURLFile($photoPath) // Envio seguro do arquivo
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['code' => $httpCode, 'response' => json_decode($response, true)];
    }
}
