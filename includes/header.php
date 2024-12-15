<?php
require 'includes/db_connect.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProFácil</title>
    <link rel="stylesheet" href="assets/css/front.css">
</head>
<body>
    <header>
        <h1>ProFácil</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="list_professionals.php">Profissionais</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Perfil</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
