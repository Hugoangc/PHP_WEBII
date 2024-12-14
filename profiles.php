<?php
include 'includes/header.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 

    $stmt = $pdo->prepare("
        SELECT profiles.*, users.fullname, services.service_name, services.description, services.price 
        FROM profiles
        LEFT JOIN users ON profiles.user_id = users.id
        LEFT JOIN services ON profiles.id = services.profile_id
        WHERE profiles.id = :id
    ");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); 

    $stmt->execute();
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        echo "Perfil não encontrado.";
        exit;
    }

    $stmt_services = $pdo->prepare("
    SELECT service_name, description, price
    FROM services
    WHERE profile_id = :id");
    $stmt_services->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_services->execute();
    $services = $stmt_services->fetchAll(PDO::FETCH_ASSOC);
    if ($services === false) {
        $services = [];
    }
} else {
    echo "ID nao encontrado.";
    exit;
}
?>
<main>
    <div class="profile-container">
        <h2><?php echo htmlspecialchars($profile['profession']); ?></h2>
        <h2><?php echo htmlspecialchars($profile['fullname']); ?></h2>
        <p><strong>Profissão:</strong> <?php echo htmlspecialchars($profile['profession']); ?></p>
        <p><strong>Bio:</strong> <?php echo htmlspecialchars($profile['bio']); ?></p>
        <p><strong>Localização:</strong> <?php echo htmlspecialchars($profile['location']); ?></p>
        <p><strong>Contato:</strong> <?php echo htmlspecialchars($profile['contact_info']); ?></p>

        <h3>Serviços</h3>
        <?php if (count($services) > 0): ?>
            <ul>
                <?php foreach ($services as $service): ?>
                    <li>
                        <strong>Serviço:</strong> <?php echo htmlspecialchars($service['service_name']); ?><br>
                        <strong>Descrição:</strong> <?php echo htmlspecialchars($service['description']); ?><br>
                        <strong>Preço:</strong> $<?php echo htmlspecialchars($service['price']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Nenhum serviço para esse perfil.</p>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

