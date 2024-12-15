<?php
include 'includes/header.php';



if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professional') {
  header("Location: login.php");
  exit();
}

$stmt = $pdo->prepare("SELECT id FROM profiles WHERE user_id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$profile = $stmt->fetch();

if (!$profile) {
  die("Você precisa criar um perfil antes de gerenciar serviços.");
}

$profile_id = $profile['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_service'])) {
  $service_name = $_POST['service_name'];
  $description = $_POST['description'];
  $price = $_POST['price'];

  $stmt = $pdo->prepare("INSERT INTO services (profile_id, service_name, description, price) VALUES (:profile_id, :service_name, :description, :price)");
  $stmt->execute([
      'profile_id' => $profile_id,
      'service_name' => $service_name,
      'description' => $description,
      'price' => $price
  ]);

  header("Location: edit_services.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_service'])) {
  $service_id = $_POST['service_id'];
  $service_name = $_POST['service_name'];
  $description = $_POST['description'];
  $price = $_POST['price'];

  $stmt = $pdo->prepare("UPDATE services SET service_name = :service_name, description = :description, price = :price WHERE id = :id AND profile_id = :profile_id");
  $stmt->execute([
      'service_name' => $service_name,
      'description' => $description,
      'price' => $price,
      'id' => $service_id,
      'profile_id' => $profile_id
  ]);

  header("Location: edit_services.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_service'])) {
  $service_id = $_POST['service_id'];

  $stmt = $pdo->prepare("DELETE FROM services WHERE id = :id AND profile_id = :profile_id");
  $stmt->execute(['id' => $service_id, 'profile_id' => $profile_id]);

  header("Location: edit_services.php");
  exit();
}

$stmt = $pdo->prepare("SELECT * FROM services WHERE profile_id = :profile_id");
$stmt->execute(['profile_id' => $profile_id]);
$services = $stmt->fetchAll();

?>
<link rel="stylesheet" href="assets/css/edit_services.css">
<main>
    <h2>Gerenciar Serviços</h2>

    <section class="service-form">
        <h3>Adicionar Novo Serviço</h3>
        <form method="POST" class="form-container">
            <label for="service_name">Nome do Serviço:</label>
            <input type="text" id="service_name" name="service_name" required>

            <label for="description">Descrição:</label>
            <textarea id="description" name="description" required></textarea>

            <label for="price">Preço:</label>
            <input type="number" id="price" name="price" step="0.01" required>

            <button type="submit" name="add_service" class="btn btn-add">Adicionar Serviço</button>
        </form>
    </section>

    <section class="service-list">
        <h3>Seus Serviços</h3>
        <?php if (count($services) > 0): ?>
            <table class="service-table">
                <thead>
                    <tr>
                        <th>Nome do Serviço</th>
                        <th>Descrição</th>
                        <th>Preço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <form method="POST">
                                <td>
                                    <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                    <input type="text" name="service_name" value="<?= $service['service_name'] ?>" required>
                                </td>
                                <td>
                                    <textarea name="description" required><?= $service['description'] ?></textarea>
                                </td>
                                <td>
                                    <input type="number" name="price" step="0.01" value="<?= $service['price'] ?>" required>
                                </td>
                                <td>
                                    <button type="submit" name="edit_service" class="btn btn-edit">Salvar</button>
                                    <button type="submit" name="delete_service" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir este serviço?');">Excluir</button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Você ainda não adicionou nenhum serviço.</p>
        <?php endif; ?>
    </section>
    <button onclick="window.history.back();" class="btn-secondary">Voltar</button>

</main>

<?php include 'includes/footer.php'; ?>