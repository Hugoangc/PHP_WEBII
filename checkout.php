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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_number = trim($_POST['card_number']);
    $card_name = trim($_POST['card_name']);
    $expiry_date = trim($_POST['expiry_date']);
    $cvv = trim($_POST['cvv']);

    if (strlen($card_number) === 16 && ctype_digit($card_number) && !empty($card_name) && strlen($cvv) === 3) {
      // Salvar pedido no banco de dados
      $stmt_order = $pdo->prepare("
          INSERT INTO orders (user_id, service_id, payment_status) 
          VALUES (:user_id, :service_id, 'pago')
      ");
      $stmt_order->execute([
          'user_id' => $_SESSION['user_id'], // Assumindo que o ID do usuário logado está na sessão
          'service_id' => $service_id
      ]);
  
      echo "<p>Pagamento validado com sucesso! Serviço contratado.</p>";
  } else {
      echo "<p>Erro: Dados do cartão inválidos.</p>";
  }
}
?>

<main>
<h2>Checkout - <?php echo htmlspecialchars($service['service_name']); ?></h2>
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