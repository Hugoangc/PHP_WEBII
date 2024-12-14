<?php
require 'includes/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'professional') {
    header("Location: login.php");
    exit();
}

include 'includes/header.php';
?>
<main>
    <h2>Dashboard</h2>
    <p>Bem-vindo(a), <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User'; ?>!</p>
    <a href="edit_profile.php">Edit Profile</a>
    <a href="logout.php">Logout</a>
</main>
<?php include 'includes/footer.php'; ?>
