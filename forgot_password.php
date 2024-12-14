<?php
include 'includes/header.php'; 
require 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Consulta ao banco de dados para encontrar o usuário
    $stmt = $pdo->prepare("SELECT password FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        $to = $email;
        $subject = "Recupercao de senha";
        $message = "Sua senha atual e: " . $user['password'];
        $headers = "From: no-reply@phpwebii.com";

        if (mail($to, $subject, $message, $headers)) {
            echo "<p>Enviado com sucesso e-mail para $email.</p>";
        } else {
            echo "<p>Falha ao enviar o e-mail. Tente novamente mais tarde.</p>";
        }
    } else {
        echo "<p>Email nao encontrado no sistema.</p>";
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
