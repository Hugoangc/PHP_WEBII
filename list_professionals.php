<?php
include 'includes/header.php';

$filter_profession = isset($_GET['profession']) ? $_GET['profession'] : null;

$professions_stmt = $pdo->query("SELECT DISTINCT profession FROM profiles");
$professions = $professions_stmt->fetchAll(PDO::FETCH_ASSOC);

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
            <option value="">Todas</option>
            <?php foreach ($professions as $prof): ?>
                <option value="<?= htmlspecialchars($prof['profession']) ?>" 
                        <?= $filter_profession == $prof['profession'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($prof['profession']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filtrar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Profissão</th>
                <th>Localização</th>
                <th>Contacto</th>
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

<?php include 'includes/footer.php'; ?>