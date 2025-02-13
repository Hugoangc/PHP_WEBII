<?php
include 'includes/header.php';

// serviços contratados de você
$stmt = $pdo->prepare("
    SELECT orders.id AS order_id, services.service_name, services.description, services.price, 
           users.fullname AS client_name, orders.created_at, profiles.id AS profile_id, users.id AS client_id
    FROM orders
    INNER JOIN services ON orders.service_id = services.id
    INNER JOIN profiles ON services.profile_id = profiles.id
    INNER JOIN users ON orders.user_id = users.id
    WHERE profiles.user_id = :user_id
    ORDER BY orders.created_at DESC
");
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();

$received_contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h2>Serviços Contratados de Mim</h2>
    <?php if (count($received_contracts) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th>Cliente</th>
                    <th>Preço</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
    <?php foreach ($received_contracts as $contract): ?>
        <tr>
            <td><?php echo htmlspecialchars($contract['service_name']); ?></td>
            <td>
                <a href="profiles.php?id=<?php echo htmlspecialchars($contract['client_id']); ?>">
                    <?php echo htmlspecialchars($contract['client_name']); ?>
                </a>
            </td>
            <td>R$ <?php echo htmlspecialchars($contract['price']); ?></td>
            <td><?php echo htmlspecialchars($contract['created_at']); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum serviço foi contratado de você.</p>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
