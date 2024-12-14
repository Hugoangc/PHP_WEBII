<?php
include 'includes/header.php'; 

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verificar se o token é válido
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE reset_token = :token AND token_expiry > NOW()");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch();

    if ($user) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $newPassword = $_POST['new_password'];
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); 

            $stmt = $pdo->prepare("UPDATE users SET password = :password, reset_token = NULL, token_expiry = NULL WHERE id = :id");
            $stmt->execute(['password' => $hashedPassword, 'id' => $user['id']]);

            echo "<p>Senha redefinida com sucesso! Agora você pode <a href='login.php'>fazer login</a>.</p>";
        }

        echo '<form method="POST" action="">
                <label for="new_password">Nova Senha</label>
                <input type="password" id="new_password" name="new_password" required>
                <button type="submit">Redefinir Senha</button>
              </form>';
    } else {
        echo "<p>Token inválido ou expirado.</p>";
    }
} else {
    echo "<p>Token não fornecido.</p>";
}

include 'includes/footer.php';
?>
