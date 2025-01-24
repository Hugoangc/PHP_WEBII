<?php
include 'includes/header.php';

$stmt = $pdo->prepare("
    SELECT orders.id AS order_id, services.service_name, services.description, services.price, 
           profiles.profession, users.fullname AS provider_name, orders.created_at
    FROM orders
    INNER JOIN services ON orders.service_id = services.id
    INNER JOIN profiles ON services.profile_id = profiles.id
    INNER JOIN users ON profiles.user_id = users.id
    WHERE orders.user_id = :user_id
    ORDER BY orders.created_at DESC
");
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();

$my_contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h2>Minhas Contratações</h2>
    <?php if (count($my_contracts) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th>Profissão</th>
                    <th>Fornecedor</th>
                    <th>Preço</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($my_contracts as $contract): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($contract['service_name']); ?></td>
                        <td><?php echo htmlspecialchars($contract['profession']); ?></td>
                        <td><?php echo htmlspecialchars($contract['provider_name']); ?></td>
                        <td>R$ <?php echo htmlspecialchars($contract['price']); ?></td>
                        <td><?php echo htmlspecialchars($contract['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Você ainda não contratou nenhum serviço.</p>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
