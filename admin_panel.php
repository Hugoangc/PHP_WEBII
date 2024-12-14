<?php
require 'includes/db_connect.php';

session_start();
if ($_SESSION['user_type'] !== 'admin') {
    header("Location: index.php");
    exit();
}
//Nem ideia ainda dessa página
include 'includes/header.php';
?>
    <main>
        <h2>Gerencie os Usuários</h2>
        <ul>
            <li><a href="#">Approve/Reject Profiles</a></li>
            <li><a href="#">View Reports</a></li>
            <li><a href="#">Manage Users</a></li>
        </ul>
    </main>
<?php include 'includes/footer.php'; ?>
