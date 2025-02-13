<?php
include 'includes/header.php';

// Obter o filtro de profissão, se houver
$filter_profession = isset($_GET['profession']) ? $_GET['profession'] : null;

// Obter todas as profissões disponíveis para o filtro
$professions_stmt = $pdo->query("SELECT DISTINCT profession FROM profiles");
$professions = $professions_stmt->fetchAll(PDO::FETCH_ASSOC);

// Construir a consulta para obter os profissionais, excluindo 'user' e 'admin'
$query = "SELECT users.id, users.fullname, profiles.profession, profiles.location, profiles.contact_info 
          FROM users 
          JOIN profiles ON users.id = profiles.user_id
          WHERE users.role = 'professional'";  // Excluir usuários 'user' e 'admin'

if ($filter_profession) {
    // Se houver filtro de profissão, adicionar à consulta
    $query .= " AND profiles.profession = :profession";
}

$stmt = $pdo->prepare($query);

// Executar a consulta com ou sem o filtro de profissão
if ($filter_profession) {
    $stmt->execute(['profession' => $filter_profession]);
} else {
    $stmt->execute();
}

$professionals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="list_professionals.css">

<main class="list-professionals">
    <h2>Lista de Profissionais</h2>

    <form method="GET" action="list_professionals.php" class="filter-form">
        <label for="profession">Filtrar por profissão:</label>
        <select id="profession" name="profession">
            <option value="">Todas</option>
            <?php foreach ($professions as $prof): ?>
                <option value="<?= htmlspecialchars($prof['profession']) ?>" 
                        <?= $filter_profession == $prof['profession'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($prof['profession']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-filter">Filtrar</button>
    </form>

    <table class="professionals-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Profissão</th>
                <th>Localização</th>
                <th>Contato</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($professionals as $prof): ?>
                <tr>
                    <td>
                        <a href="profiles.php?id=<?= htmlspecialchars($prof['id']) ?>" class="profile-link">
                            <?= htmlspecialchars($prof['fullname']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($prof['profession']) ?></td>
                    <td><?= htmlspecialchars($prof['location'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($prof['contact_info'] ?? 'N/A') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php include 'includes/footer.php'; ?>
