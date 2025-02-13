<?php
include 'includes/header.php';

// Verificar se o usuário tem permissão de administrador
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Verificar se o ID do usuário foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do usuário não fornecido.");
}

$user_id = $_GET['id'];

// Buscar o usuário no banco de dados
$stmt = $pdo->prepare("SELECT id, fullname, email, role FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Se o usuário não for encontrado
if (!$user) {
    die("Usuário não encontrado.");
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Atualizar o usuário no banco de dados
    $stmt_update = $pdo->prepare("UPDATE users SET fullname = :fullname, email = :email, role = :role WHERE id = :id");
    $stmt_update->execute([
        'fullname' => $fullname,
        'email' => $email,
        'role' => $role,
        'id' => $user_id
    ]);

    // Redirecionar para o painel após a edição
    header("Location: admin_panel.php");
    exit();
}
?>

<main>
    <h2>Editar Usuário</h2>
    <form method="POST" action="admin_edit_user.php?id=<?= htmlspecialchars($user['id']) ?>">
        <label for="fullname">Nome:</label>
        <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label for="role">Papel:</label>
        <select id="role" name="role">
            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="professional" <?= $user['role'] == 'professional' ? 'selected' : '' ?>>Profissional</option>
            <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>Usuário</option>
        </select>

        <button type="submit">Atualizar</button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
