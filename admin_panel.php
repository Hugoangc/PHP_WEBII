


<?php
include 'includes/header.php'; // Inclui o cabeçalho
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redireciona para uma página de erro ou login se não for administrador
    header('Location: login.php');
    exit;
}

?>

<main>
    <link rel="stylesheet" href="assets/css/admin.css">

    <h2>Painel de Administração</h2>

    <!-- Exibição de Usuários -->
    <section>
        <h3>Usuários</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Data de Registro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Buscar usuários
                $stmt_users = $pdo->prepare("SELECT id, fullname, email, role, created_at FROM users");
                $stmt_users->execute();
                $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

                foreach ($users as $user) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['fullname']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
                    echo "<td><a href='admin_edit_user.php?id=" . $user['id'] . "'>Editar</a> | <a href='admin_delete_user.php?id=" . $user['id'] . "'>Excluir</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </section>

    <!-- Exibição de Serviços -->
    <section>
        <h3>Serviços</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Serviço</th>
                    <th>Preço</th>
                    <th>Data de Criação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Buscar serviços
                $stmt_services = $pdo->prepare("SELECT id, service_name, price, created_at FROM services");
                $stmt_services->execute();
                $services = $stmt_services->fetchAll(PDO::FETCH_ASSOC);

                foreach ($services as $service) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($service['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($service['service_name']) . "</td>";
                    echo "<td>R$ " . number_format($service['price'], 2, ',', '.') . "</td>";
                    echo "<td>" . htmlspecialchars($service['created_at']) . "</td>";
                    echo "<td><a href='admin_delete_service.php?id=" . $service['id'] . "'>Excluir</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </section>

    <!-- Exibição de Pedidos -->
    <section>
        <h3>Pedidos</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Serviço</th>
                    <th>Usuário</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Buscar pedidos
                $stmt_orders = $pdo->prepare("SELECT o.id, s.service_name, u.fullname AS user_name, o.service_price, o.payment_status FROM orders o
                                              JOIN services s ON o.service_id = s.id
                                              JOIN users u ON o.user_id = u.id");
                $stmt_orders->execute();
                $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

                foreach ($orders as $order) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($order['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($order['service_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($order['user_name']) . "</td>";
                    echo "<td>R$ " . number_format($order['service_price'], 2, ',', '.') . "</td>";
                    echo "<td>" . htmlspecialchars($order['payment_status']) . "</td>";
                    echo "<td> <a href='admin_delete_order.php?id=" . $order['id'] . "'>Excluir</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
