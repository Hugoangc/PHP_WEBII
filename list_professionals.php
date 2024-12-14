<?php
require 'includes/db_connect.php';
include 'includes/header.php';

// Verificar se foi passado algum filtro de profissão
$filter_profession = isset($_GET['profession']) ? $_GET['profession'] : null;

// Consultar profissões únicas para os filtros
$professions_stmt = $pdo->query("SELECT DISTINCT profession FROM profiles");
$professions = $professions_stmt->fetchAll(PDO::FETCH_ASSOC);

// Consultar profissionais
$query = "SELECT users.id, users.fullname, profiles.profession, profiles.location, profiles.contact_info 
          FROM users 
          JOIN profiles ON users.id = profiles.user_id";

if ($filter_profession) {
    $query .= " WHERE profiles.profession = :profession";
}

$stmt = $pdo->prepare($query);

if ($filter_profession) {
    $stmt->execute(['profession' => $filter_profession]);
} else {
    $stmt->execute();
}

$professionals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h2>List of Professionals</h2>

    <form method="GET" action="list_professionals.php">
        <label for="profession">Filtro por profissão</label>
        <select id="profession" name="profession">
            <option value="">All</option>
            <?php foreach ($professions as $prof): ?>
                <option value="<?= htmlspecialchars($prof['profession']) ?>" 
                        <?= $filter_profession == $prof['profession'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($prof['profession']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filter</button>
    </form>

    <!-- Lista de profissionais -->
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Profession</th>
                <th>Location</th>
                <th>Contact Info</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($professionals as $prof): ?>
                <tr>
                <td>
                    <a href="profiles.php?id=<?= htmlspecialchars($prof['id']) ?>">
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