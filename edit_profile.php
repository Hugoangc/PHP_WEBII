<?php
include 'includes/header.php'; 


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'professional') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['delete_account']) && $_POST['delete_account'] == 1) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
    
        session_destroy(); 
        header("Location: goodbye.php"); 
        exit();
    }
    
    $fullname = $_POST['fullname'];
    $profession = $_POST['profession'];
    $location = $_POST['location'];
    $contact_info = $_POST['contact_info'];
    $bio = $_POST['bio'];

    $stmt = $pdo->prepare("UPDATE profiles SET profession = :profession, location = :location, contact_info = :contact_info, bio = :bio WHERE user_id = :user_id");
    $stmt->execute([
        'profession' => $profession,
        'location' => $location,
        'contact_info' => $contact_info,
        'bio' => $bio,
        'user_id' => $user_id
    ]);

    $stmt = $pdo->prepare("UPDATE users SET fullname = :fullname WHERE id = :user_id");
    $stmt->execute([
        'fullname' => $fullname,
        'user_id' => $user_id
    ]);
    
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :user_id");
        $stmt->execute(['password' => $password, 'user_id' => $user_id]);
    }

    echo "<p>Perfil atualizado com sucesso!</p>";


}

$stmt = $pdo->prepare("SELECT p.*, u.fullname FROM profiles p JOIN users u ON p.user_id = u.id WHERE p.user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$profile = $stmt->fetch();
?>
<main>
    <link rel="stylesheet" href="assets/css/edit_profile.css">
    <h2>Edit Profile</h2>
    <form method="POST" action="" class="profile-form">
        <div class="form-group">
            <label for="fullname">Nome completo</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($profile['fullname']); ?>" required>
        </div>

        <div class="form-group">
            <label for="profession">Profissão</label>
            <input type="text" id="profession" name="profession" value="<?php echo htmlspecialchars($profile['profession']); ?>" required>
        </div>

        <div class="form-group">
            <label for="location">Localização</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($profile['location']); ?>">
        </div>

        <div class="form-group">
            <label for="contact_info">Informação de contato</label>
            <input type="text" id="contact_info" name="contact_info" value="<?php echo htmlspecialchars($profile['contact_info']); ?>">
        </div>

        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio"><?php echo htmlspecialchars($profile['bio']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="password">Nova senha (opcional)</label>
            <input type="password" id="password" name="password">
        </div>

        <button type="submit" class="btn-primary">Salvar</button>
    </form>
    <div class="action-buttons">
    <button onclick="window.history.back();" class="btn-secondary">Voltar</button>

</div>
    <div class="action-buttons">

        <form method="POST" action="" onsubmit="return confirm('Tem certeza que deseja deletar sua conta? Esta ação não pode ser desfeita.');">
            <input type="hidden" name="delete_account" value="1">
            <button type="submit" class="btn-danger">Deletar Conta</button>
        </form>
    </div>
</main>
<?php include 'includes/footer.php'; ?>

