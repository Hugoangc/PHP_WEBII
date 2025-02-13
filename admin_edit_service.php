<?php
include 'includes/header.php'; // Inclui o cabeçalho

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Verifica se o ID do serviço foi passado via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do serviço não fornecido.");
}

$service_id = $_GET['id'];

// Buscar o serviço no banco de dados
$stmt = $pdo->prepare("SELECT id, service_name, price FROM services WHERE id = :id");
$stmt->execute(['id' => $service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

// Se o serviço não existir, exibir mensagem de erro
if (!$service) {
    die("Serviço não encontrado.");
}

// Processar o formulário para atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_name = $_POST['service_name'];
    $price = $_POST['price'];

    // Atualiza os dados do serviço no banco de dados
    $stmt_update = $pdo->prepare("UPDATE services SET service_name = :service_name, price = :price WHERE id = :id");
    $stmt_update->execute([
        'service_name' => $service_name,
        'price' => $price,
        'id' => $service_id
    ]);

    // Redireciona de volta para o painel de administração
    header("Location: admin_panel.php");
    exit();
}
?>

<main>
    <h2>Editar Serviço</h2>
    <form method="POST">
        <label for="service_name">Nome do Serviço:</label>
        <input type="text" id="service_name" name="service_name" value="<?= htmlspecialchars($service['service_name']) ?>" required>

        <label for="price">Preço:</label>
        <input type="text" id="price" name="price" value="<?= number_format($service['price'], 2, ',', '.') ?>" required>

        <button type="submit">Atualizar</button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
