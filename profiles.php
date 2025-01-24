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
<link rel="stylesheet" href="profile.css">
<main>
    <div class="profile-container">
        <div class="profile-header">
            <h2><?php echo htmlspecialchars($profile['fullname']); ?></h2>
            <p class="profession"><?php echo htmlspecialchars($profile['profession']); ?></p>
        </div>
        
        <div class="profile-info">
            <p><strong>Bio:</strong> <?php echo htmlspecialchars($profile['bio']); ?></p>
            <p><strong>Localização:</strong> <?php echo htmlspecialchars($profile['location']); ?></p>
            <p><strong>Contato:</strong> <?php echo htmlspecialchars($profile['contact_info']); ?></p>
        </div>

        <div class="profile-services">
            <h3>Serviços Oferecidos</h3>
            <?php if (count($services) > 0): ?>
                <table class="service-table">
                    <thead>
                        <tr>
                            <th>Serviço</th>
                            <th>Descrição</th>
                            <th>Preço</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                                <td><?php echo htmlspecialchars($service['description']); ?></td>
                                <td>R$ <?php echo htmlspecialchars($service['price']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum serviço para esse perfil.</p>
            <?php endif; ?>
        </div>
        
        <button onclick="window.history.back();" class="btn-secondary">Voltar</button>
    </div>
    <a href="chat.php?id=<?php echo $profile['user_id']; ?>" class="btn-primary">Iniciar Chat</a>

</main>

<?php include 'includes/footer.php'; ?>

