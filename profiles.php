<?php
include 'includes/header.php';

function get_lat_long($cidade, $estado = null) {
    $location = $estado ? $cidade . ', ' . $estado : $cidade;

    $location = urlencode($location);

    $url = "https://nominatim.openstreetmap.org/search?q={$location}&format=json&addressdetails=1&countrycodes=BR";

    $options = [
        "http" => [
            "header" => "User-Agent: MeuApp/1.0 (meuemail@dominio.com)\r\n"
        ]
    ];
    $context = stream_context_create($options);

    $response = file_get_contents($url, false, $context);

    if ($response) {
        $data = json_decode($response, true);

        if (!empty($data)) {
            $latitude = $data[0]['lat'];
            $longitude = $data[0]['lon'];

            return array('latitude' => $latitude, 'longitude' => $longitude);
        }
    }

    return null; 
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 

    $stmt = $pdo->prepare("SELECT profiles.*, users.fullname, users.role, services.service_name, services.description, services.price 
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
        SELECT id, service_name, description, price
        FROM services
        WHERE profile_id = :id
    ");
    $stmt_services->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_services->execute();
    $services = $stmt_services->fetchAll(PDO::FETCH_ASSOC);
    if ($services === false) {
        $services = [];
    }
} else {
    echo "ID não encontrado.";
    exit;
}

$cidade = $profile['location']; 

$location_parts = explode(',', $cidade);
$city = trim($location_parts[0]);
$state = isset($location_parts[1]) ? trim($location_parts[1]) : null;

$coordenadas = get_lat_long($city, $state);

if ($coordenadas) {
    $latitude = $coordenadas['latitude'];
    $longitude = $coordenadas['longitude'];
} else {
    $latitude = -23.550520; // São Paulo como fallback
    $longitude = -46.633308; // São Paulo como fallback
}

$role = isset($profile['role']) ? $profile['role'] : 'user'; // Verifica o role do usuário no banco de dados
?>

<link rel="stylesheet" href="assets/css/profile.css">

<!-- Carregar Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var map = L.map('map').setView([<?php echo $latitude; ?>, <?php echo $longitude; ?>], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    L.marker([<?php echo $latitude; ?>, <?php echo $longitude; ?>])
        .addTo(map)
        .bindPopup("<b><?php echo htmlspecialchars($profile['fullname']); ?></b><br /><?php echo htmlspecialchars($profile['location']); ?>")
        .openPopup();
});
</script>

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

        <!-- Verifica se há serviços -->
        <?php if (count($services) > 0): ?>
            <div class="profile-services">
                <h3>Serviços Oferecidos</h3>
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
                            <?php $logged_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;?>
                            <tr>
                                <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                                <td><?php echo htmlspecialchars($service['description']); ?></td>
                                <td>R$ <?php echo htmlspecialchars($service['price']); ?></td>
                                <?php if ($logged_user_id !== $profile['user_id']): ?>
                            <td><a href="checkout.php?service_id=<?php echo $service['id']; ?>" class="btn btn-primary">Contratar</a></td>
                        <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>Este perfil não oferece serviços.</p>
        <?php endif; ?>
        
        <br><br>
        <div class="buttons">
            <button onclick="window.history.back();" class="btn-secondary">Voltar</button>
            <a href="chat.php?id=<?php echo $profile['user_id']; ?>" class="btn-secondary"><button>Iniciar Chat</button></a>
        </div>
    </div>
    
    <!-- Mapa Exibido aqui -->
    <div id="map" style="height: 400px; width: 100%;"></div>
</main>

<?php include 'includes/footer.php'; ?>
