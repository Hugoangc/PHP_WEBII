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

// Excluir o usuário do banco de dados
$stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);

// Redirecionar para o painel após a exclusão
header("Location: admin_panel.php");
exit();
?>
