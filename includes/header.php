<?php
require 'includes/db_connect.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="assets/css/front.css">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProFácil</title>
</head>
<body>
    <header>
        <img class="logo" src="assets/images/logo.png" alt="ProFácil">
            <div class="menu">
                <a href="index.php"><h1>Home</h1></a>
                <a href="list_professionals.php"><h1>Profissionais</h1></a>
            </div>
            <div class="buttons">
            <?php if (isset($_SESSION['user_id'])): ?>
                <button class="login"><a href="dashboard.php"><h1>Perfil</h1></a></button>
                <button class="register"><a href="logout.php"><h1>Logout</h1></a></button>
            <?php else: ?>
                <button class="login"><a href="login.php"><h1>Login</h1></a></button>
                <button class="register"><a href="register.php"><h1>Register</h1></a></button>
            <?php endif; ?>
            </div>

    </header>
