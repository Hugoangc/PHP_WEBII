<?php 
include 'includes/header.php'; 
require 'includes/db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Busca o usuário pelo e-mail
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Verifica a senha e cria a sessão
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Redireciona para o dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<main>
    <h2>Login</h2>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>
    <form action="" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
    </form>
    <a href="forgot_password.php">Esqueceu a senha?</a>
</main>

<?php include 'includes/footer.php'; ?>
