<?php
include 'includes/header.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mail = new PHPMailer(true);

    $email = $_POST['email'];

    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(16)); // Gera um token aleatório de 32 caracteres (16 bytes)
        $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, token_expiry = NOW() + INTERVAL 1 HOUR WHERE id = :id");
        $stmt->execute(['token' => $token, 'id' => $user['id']]);
        try {
            // Configurações do servidor SMTP
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      // Habilitar saída de debug
            $mail->isSMTP();                                            // Enviar usando SMTP
            $mail->Host       = 'smtp.gmail.com';                        // Servidor SMTP
            $mail->SMTPAuth   = true;                                    // Habilitar autenticação SMTP
            $mail->Username   = 'cefetwebii@gmail.com';                  // Usuário SMTP
            $mail->Password   = 'nmyr dfif cboh gjsd';                                // Senha SMTP
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Habilitar encriptação TLS implícita
            $mail->Port       = 465;                                      

            // Destinatário
            $mail->setFrom('cefetwebii@gmail.com', 'Mailer');
            $mail->addAddress($email);                                  
            $mail->addReplyTo('cefetwebii@gmail.com', 'Information');
            $resetLink = "http://localhost/PHP_WEBII/new_password.php?token=" . $token;

            // Conteúdo do e-mail
            $mail->isHTML(true);                                        
            $mail->Subject = "Recuperacao de senha";
            $mail->Body    = "Clique no link abaixo para redefinir sua senha: <a href=\"$resetLink\">Redefinir Senha</a>";
            $mail->AltBody = "Clique no link abaixo para redefinir sua senha: $resetLink";

            // Enviar o e-mail
            $mail->send();
            echo "<p>Enviado com sucesso o e-mail para $email.</p>";
        } catch (Exception $e) {
            echo "<p>Falha ao enviar o e-mail. Erro: {$mail->ErrorInfo}</p>";
        }
    } else {
        echo "<p>E-mail não encontrado no sistema.</p>";
    }
}
?>

<main>
    <h2>Recuperar senha</h2>
    <form method="POST" action="">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Send Password</button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
