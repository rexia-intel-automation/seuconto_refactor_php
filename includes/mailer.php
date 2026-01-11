<?php
/**
 * Sistema de Envio de E-mails
 *
 * Configuração e funções para envio de e-mails usando PHPMailer ou mail() nativo
 */

// Carrega variáveis de ambiente se ainda não carregadas
if (!function_exists('env')) {
    require_once __DIR__ . '/../config/env.php';
}

/**
 * Envia e-mail usando PHPMailer (se disponível) ou mail() nativo
 *
 * @param string $to Destinatário
 * @param string $subject Assunto
 * @param string $body Corpo do e-mail (HTML)
 * @param string $fromName Nome do remetente (opcional)
 * @param string $fromEmail E-mail do remetente (opcional)
 * @return bool True se enviado com sucesso
 */
function sendEmail($to, $subject, $body, $fromName = null, $fromEmail = null) {
    $fromName = $fromName ?? env('SMTP_FROM_NAME', 'Seu Conto');
    $fromEmail = $fromEmail ?? env('SMTP_FROM_EMAIL', 'noreply@seuconto.com.br');

    // Tenta usar PHPMailer se disponível
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return sendEmailWithPhpMailer($to, $subject, $body, $fromName, $fromEmail);
    }

    // Fallback para mail() nativo
    return sendEmailWithNativeMail($to, $subject, $body, $fromName, $fromEmail);
}

/**
 * Envia e-mail usando PHPMailer
 *
 * @param string $to Destinatário
 * @param string $subject Assunto
 * @param string $body Corpo do e-mail (HTML)
 * @param string $fromName Nome do remetente
 * @param string $fromEmail E-mail do remetente
 * @return bool True se enviado com sucesso
 */
function sendEmailWithPhpMailer($to, $subject, $body, $fromName, $fromEmail) {
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        // Configuração SMTP
        if (env('SMTP_HOST')) {
            $mail->isSMTP();
            $mail->Host = env('SMTP_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth = true;
            $mail->Username = env('SMTP_USERNAME');
            $mail->Password = env('SMTP_PASSWORD');
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = env('SMTP_PORT', 587);
        } else {
            $mail->isMail();
        }

        // Remetente e destinatário
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to);

        // Conteúdo
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Envia e-mail usando mail() nativo do PHP
 *
 * @param string $to Destinatário
 * @param string $subject Assunto
 * @param string $body Corpo do e-mail (HTML)
 * @param string $fromName Nome do remetente
 * @param string $fromEmail E-mail do remetente
 * @return bool True se enviado com sucesso
 */
function sendEmailWithNativeMail($to, $subject, $body, $fromName, $fromEmail) {
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        "From: {$fromName} <{$fromEmail}>",
        "Reply-To: {$fromEmail}",
        'X-Mailer: PHP/' . phpversion()
    ];

    return mail($to, $subject, $body, implode("\r\n", $headers));
}

/**
 * Envia e-mail de boas-vindas para novo usuário
 *
 * @param string $userEmail E-mail do usuário
 * @param string $userName Nome do usuário
 * @return bool True se enviado com sucesso
 */
function sendWelcomeEmail($userEmail, $userName) {
    $subject = 'Bem-vindo ao Seu Conto! 🎉';

    $body = getEmailTemplate('welcome', [
        'userName' => $userName,
        'loginUrl' => (defined('BASE_URL') ? BASE_URL : '') . '/pages/auth/login.php',
        'createBookUrl' => (defined('BASE_URL') ? BASE_URL : '') . '/pages/criar.php'
    ]);

    return sendEmail($userEmail, $subject, $body);
}

/**
 * Envia e-mail de confirmação de pedido
 *
 * @param string $userEmail E-mail do usuário
 * @param string $userName Nome do usuário
 * @param array $orderData Dados do pedido
 * @return bool True se enviado com sucesso
 */
function sendOrderConfirmationEmail($userEmail, $userName, $orderData) {
    $subject = 'Pedido Confirmado - Seu livro está sendo criado! ✨';

    $body = getEmailTemplate('order-confirmation', [
        'userName' => $userName,
        'orderId' => $orderData['id'] ?? '',
        'childName' => $orderData['child_name'] ?? '',
        'theme' => ucfirst($orderData['theme'] ?? ''),
        'estimatedTime' => '30 minutos',
        'trackUrl' => (defined('BASE_URL') ? BASE_URL : '') . '/pages/dashboard.php'
    ]);

    return sendEmail($userEmail, $subject, $body);
}

/**
 * Envia e-mail notificando que o livro está pronto
 *
 * @param string $userEmail E-mail do usuário
 * @param string $userName Nome do usuário
 * @param array $orderData Dados do pedido
 * @return bool True se enviado com sucesso
 */
function sendBookReadyEmail($userEmail, $userName, $orderData) {
    $subject = 'Seu livro está pronto! 📚🎉';

    $body = getEmailTemplate('book-ready', [
        'userName' => $userName,
        'childName' => $orderData['child_name'] ?? '',
        'downloadUrl' => $orderData['book_file_url'] ?? '',
        'dashboardUrl' => (defined('BASE_URL') ? BASE_URL : '') . '/pages/dashboard.php'
    ]);

    return sendEmail($userEmail, $subject, $body);
}

/**
 * Envia e-mail de recuperação de senha
 *
 * @param string $userEmail E-mail do usuário
 * @param string $userName Nome do usuário
 * @param string $resetToken Token de recuperação
 * @return bool True se enviado com sucesso
 */
function sendPasswordResetEmail($userEmail, $userName, $resetToken) {
    $subject = 'Recuperação de Senha - Seu Conto';

    $resetUrl = (defined('BASE_URL') ? BASE_URL : '') . '/pages/auth/reset-password.php?token=' . urlencode($resetToken);

    $body = getEmailTemplate('password-reset', [
        'userName' => $userName,
        'resetUrl' => $resetUrl,
        'expirationTime' => '1 hora'
    ]);

    return sendEmail($userEmail, $subject, $body);
}

/**
 * Obtém template de e-mail
 *
 * @param string $templateName Nome do template
 * @param array $vars Variáveis do template
 * @return string HTML do e-mail
 */
function getEmailTemplate($templateName, $vars = []) {
    // Extrai variáveis para o escopo local
    extract($vars);

    // Verifica se existe arquivo de template
    $templateFile = __DIR__ . '/../templates/emails/' . $templateName . '.php';

    if (file_exists($templateFile)) {
        ob_start();
        include $templateFile;
        return ob_get_clean();
    }

    // Template padrão inline se arquivo não existir
    return getDefaultEmailTemplate($templateName, $vars);
}

/**
 * Retorna template de e-mail padrão (inline)
 *
 * @param string $templateName Nome do template
 * @param array $vars Variáveis do template
 * @return string HTML do e-mail
 */
function getDefaultEmailTemplate($templateName, $vars) {
    $baseUrl = defined('BASE_URL') ? BASE_URL : '';

    // Template base
    $header = '
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body { margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5; }
            .container { max-width: 600px; margin: 0 auto; background: white; }
            .header { background: linear-gradient(135deg, #8B5CF6, #EC4899); padding: 40px 20px; text-align: center; }
            .header h1 { color: white; margin: 0; font-size: 28px; }
            .content { padding: 40px 30px; }
            .button { display: inline-block; background: #8B5CF6; color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0; }
            .footer { background: #f9f9f9; padding: 30px; text-align: center; color: #666; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>📚 Seu Conto</h1>
            </div>
            <div class="content">
    ';

    $footer = '
            </div>
            <div class="footer">
                <p>Este é um e-mail automático, por favor não responda.</p>
                <p>Precisa de ajuda? Contate-nos em contato@seuconto.com.br</p>
                <p>&copy; ' . date('Y') . ' Seu Conto. Todos os direitos reservados.</p>
            </div>
        </div>
    </body>
    </html>
    ';

    // Conteúdo específico de cada template
    switch ($templateName) {
        case 'welcome':
            $content = "
                <h2>Olá, {$vars['userName']}! 👋</h2>
                <p>Seja bem-vindo ao <strong>Seu Conto</strong>!</p>
                <p>Estamos muito felizes em tê-lo conosco. Agora você pode criar histórias mágicas personalizadas para as crianças que você ama.</p>
                <p style='text-align: center;'>
                    <a href='{$vars['createBookUrl']}' class='button'>Criar Meu Primeiro Livro</a>
                </p>
                <p>Se tiver qualquer dúvida, estamos aqui para ajudar!</p>
            ";
            break;

        case 'order-confirmation':
            $content = "
                <h2>Pedido Confirmado! ✨</h2>
                <p>Olá, {$vars['userName']}!</p>
                <p>Seu pedido <strong>#{$vars['orderId']}</strong> foi confirmado com sucesso!</p>
                <p><strong>Detalhes do Livro:</strong></p>
                <ul>
                    <li>Personagem: {$vars['childName']}</li>
                    <li>Tema: {$vars['theme']}</li>
                    <li>Tempo estimado: {$vars['estimatedTime']}</li>
                </ul>
                <p>Estamos criando uma história mágica e única. Você receberá outro e-mail quando o livro estiver pronto!</p>
                <p style='text-align: center;'>
                    <a href='{$vars['trackUrl']}' class='button'>Acompanhar Pedido</a>
                </p>
            ";
            break;

        case 'book-ready':
            $content = "
                <h2>Seu livro está pronto! 🎉📚</h2>
                <p>Olá, {$vars['userName']}!</p>
                <p>A história de <strong>{$vars['childName']}</strong> está pronta para ser descoberta!</p>
                <p>Clique no botão abaixo para fazer o download do seu livro em PDF:</p>
                <p style='text-align: center;'>
                    <a href='{$vars['downloadUrl']}' class='button'>Baixar Meu Livro</a>
                </p>
                <p>Você também pode acessar todos os seus livros no seu dashboard.</p>
            ";
            break;

        case 'password-reset':
            $content = "
                <h2>Recuperação de Senha</h2>
                <p>Olá, {$vars['userName']}!</p>
                <p>Recebemos uma solicitação para redefinir sua senha.</p>
                <p>Clique no botão abaixo para criar uma nova senha:</p>
                <p style='text-align: center;'>
                    <a href='{$vars['resetUrl']}' class='button'>Redefinir Senha</a>
                </p>
                <p>Este link expira em {$vars['expirationTime']}.</p>
                <p>Se você não solicitou esta alteração, ignore este e-mail.</p>
            ";
            break;

        default:
            $content = '<p>Conteúdo do e-mail.</p>';
    }

    return $header . $content . $footer;
}
