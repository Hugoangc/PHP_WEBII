<?php
include 'includes/header.php';

if (!isset($_GET['service_id'])) {
    echo "Serviço não especificado.";
    exit;
}

$service_id = intval($_GET['service_id']);
$stmt = $pdo->prepare("SELECT service_name, price FROM services WHERE id = :service_id");
$stmt->execute(['service_id' => $service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    echo "Serviço não encontrado.";
    exit;
}

// Obtendo as informações do usuário logado
$user_id = $_SESSION['user_id']; // ID do usuário logado
$stmt_user = $pdo->prepare("SELECT fullname FROM users WHERE id = :user_id");
$stmt_user->execute(['user_id' => $user_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Usuário não encontrado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_number = trim($_POST['card_number']);
    $card_name = trim($_POST['card_name']);
    $expiry_date = trim($_POST['expiry_date']);
    $cvv = trim($_POST['cvv']);

    if (strlen($card_number) === 16 && ctype_digit($card_number) && !empty($card_name) && strlen($cvv) === 3) {
        // Salvar pedido no banco de dados com detalhes completos (nome e preço do serviço)
        $stmt_order = $pdo->prepare("
            INSERT INTO orders (user_id, service_id, payment_status, service_name, service_price) 
            VALUES (:user_id, :service_id, 'pago', :service_name, :service_price)
        ");
        $stmt_order->execute([
            'user_id' => $user_id, // ID do usuário logado
            'service_id' => $service_id,
            'service_name' => $service['service_name'],
            'service_price' => $service['price']
        ]);

        // Exibir confirmação de pagamento
        echo "<div class='transaction-summary'>";
        echo "<h3>Resumo da Transação</h3>";
        echo "<p><strong>Serviço:</strong> " . htmlspecialchars($service['service_name']) . "</p>";
        echo "<p><strong>Valor:</strong> R$ " . number_format($service['price'], 2, ',', '.') . "</p>";
        echo "<p><strong>Usuário:</strong> " . htmlspecialchars($user['fullname']) . "</p>";
        echo "<p><strong>Status do pagamento:</strong> Pago</p>";
        echo "</div>";
        exit;
    } else {
        echo "<p>Erro: Dados do cartão inválidos.</p>";
    }
}
?>

<main>
    <link rel="stylesheet" href="assets/css/checkout.css">

    <h2>Checkout - <?php echo htmlspecialchars($service['service_name']); ?></h2>
    
    <p><strong>Serviço:</strong> <?php echo htmlspecialchars($service['service_name']); ?></p>
    <p><strong>Valor:</strong> R$ <?php echo number_format($service['price'], 2, ',', '.'); ?></p>
    <p><strong>Usuário:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>

    <form method="POST" action="">
        <label for="card_number">Número do Cartão:</label>
        <input type="text" name="card_number" id="card_number" maxlength="16" required>

        <label for="card_name">Nome no Cartão:</label>
        <input type="text" name="card_name" id="card_name" required>

        <label for="expiry_date">Data de Validade:</label>
        <input type="month" name="expiry_date" id="expiry_date" required>

        <label for="cvv">CVV:</label>
        <input type="text" name="cvv" id="cvv" maxlength="3" required>

        <button type="submit">Validar Pagamento</button>
    </form>

    <button onclick="window.history.back();" class="btn-secondary">Voltar</button>
</main>

<?php include 'includes/footer.php'; ?>
