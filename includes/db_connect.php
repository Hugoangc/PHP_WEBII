<?php
$host = 'cefet-web.mysql.uhserver.com';
$db = 'cefet_web'; //nome do banco
$user = 'webii'; 
$pass = 'WEB11cefet@'; // senha do MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
