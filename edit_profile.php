<?php
require 'includes/db_connect.php';
include 'includes/header.php'; 

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'professional') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $profession = $_POST['profession'];
    $location = $_POST['location'];
    $contact_info = $_POST['contact_info'];
    $bio = $_POST['bio'];

    // Atualiza as informações do perfil
    $stmt = $pdo->prepare("UPDATE profiles SET profession = :profession, location = :location, contact_info = :contact_info, bio = :bio WHERE user_id = :user_id");
    $stmt->execute(['profession' => $profession, 'location' => $location, 'contact_info' => $contact_info, 'bio' => $bio, 'user_id' => $user_id]);

    // Verifica se a senha também foi enviada
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :user_id");
        $stmt->execute(['password' => $password, 'user_id' => $user_id]);
    }

    echo "<p>Perfil atualizado com sucesso!</p>";
}

// Recupera os dados do perfil para preencher o formulário
$stmt = $pdo->prepare("SELECT p.*, u.fullname FROM profiles p JOIN users u ON p.user_id = u.id WHERE p.user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$profile = $stmt->fetch();
?>
<main>
    <h2>Edit Profile</h2>
    <form method="POST" action="">
        <label for="fullname">Nome completo</label>
        <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($profile['fullname']); ?>" required>

        <label for="profession">Profissao</label>
        <input type="text" id="profession" name="profession" value="<?php echo htmlspecialchars($profile['profession']); ?>" required>

        <label for="location">Localizacao</label>
        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($profile['location']); ?>">

        <label for="contact_info">Informacao de contato</label>
        <input type="text" id="contact_info" name="contact_info" value="<?php echo htmlspecialchars($profile['contact_info']); ?>">

        <label for="bio">Bio</label>
        <textarea id="bio" name="bio"><?php echo htmlspecialchars($profile['bio']); ?></textarea>

        <label for="password">Nova senha (opcional)</label>
        <input type="password" id="password" name="password">

        <button type="submit">Salvar</button>
    </form>
</main>
<?php include 'includes/footer.php'; ?>

