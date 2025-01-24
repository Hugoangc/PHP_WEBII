<?php
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'professional') {
    header("Location: login.php");
    exit();
}


?>
<main>
    <h2>Bem-vindo(a), <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Usuário'; ?>!</h2>
    <a href="profiles.php?id=<?= htmlspecialchars($_SESSION['user_id']) ?>" class="profile-link">Visualizar meu Perfil</a>
    <a href="edit_profile.php">Editar Perfil</a>
    <a href="edit_services.php">Gerenciar Serviços</a>
    <a href="conversations.php">Minhas Conversas</a>
    <a href="logout.php">Logout</a>
</main>
<?php include 'includes/footer.php'; ?>
