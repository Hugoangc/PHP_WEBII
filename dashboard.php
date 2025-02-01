<?php
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'professional') {
    header("Location: login.php");
    exit();
}


?>
<link rel="stylesheet" href="assets/css/dashboard.css">
<main>
    <h2>Bem-vindo(a), <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Usuário'; ?>!</h2>
    <a href="myprofile.php?id=<?= htmlspecialchars($_SESSION['user_id']) ?>" class="profile-link"><button>Visualizar meu Perfil</button></a>
    <a href="edit_profile.php"><button>Editar Perfil</button></a>
    <a href="edit_services.php"><button>Gerenciar Serviços</button></a>
    <a href="conversations.php"><button>Minhas Conversas</button></a>
    <a href="logout.php"><button>Logout</button></a>
</main>
<?php include 'includes/footer.php'; ?>
