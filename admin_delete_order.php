<?php
include 'includes/header.php'; // Inclui o cabeçalho

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Verifica se o ID do pedido foi passado via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do pedido não fornecido.");
}

$order_id = $_GET['id'];

// Conecta com o banco de dados e executa a exclusão
$stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
$stmt->execute(['id' => $order_id]);

// Redireciona de volta para o painel de administração
header("Location: admin_panel.php");
exit();
?>
