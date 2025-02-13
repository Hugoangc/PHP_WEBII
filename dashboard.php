<?php
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user'; // Assumindo que a variável de sessão 'role' está armazenada

?>
<main>
    <h2>Bem-vindo(a), <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Usuário'; ?>!</h2>
    
    <!-- Visualizar meu perfil -->
    <a href="profiles.php?id=<?= htmlspecialchars($_SESSION['user_id']) ?>" class="profile-link">Visualizar meu Perfil</a>
    
    <a href="edit_profile.php">Editar Perfil</a>
    <a href="my_contracts.php">Meus contratos</a>
    <?php if ($role === 'admin'): ?>
        <a href="admin_panel.php">Pagina ADMIN</a>
    <?php endif; ?>

    <?php if ($role === 'professional'): ?>
        <!-- Só exibe o link de contratos recebidos para profissionais -->
        <a href="edit_services.php">Gerenciar Serviços</a>
        <a href="received_contracts.php">Contratos Recebidos</a>
    <?php endif; ?>

    <a href="conversations.php">Minhas Conversas</a>
    <a href="logout.php">Logout</a>
</main>
<?php include 'includes/footer.php'; ?>
