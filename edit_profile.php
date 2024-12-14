<?php
require 'includes/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'professional') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $profession = $_POST['profession'];
    $location = $_POST['location'];
    $contact_info = $_POST['contact_info'];
    $bio = $_POST['bio'];

    // Atualiza o perfil no banco de dados
    $stmt = $pdo->prepare("UPDATE profiles SET profession = :profession, location = :location, contact_info = :contact_info, bio = :bio WHERE user_id = :user_id");
    $stmt->execute([
        'profession' => $profession,
        'location' => $location,
        'contact_info' => $contact_info,
        'bio' => $bio,
        'user_id' => $user_id
    ]);

    $_SESSION['success_message'] = "Profile updated successfully!";
    header("Location: dashboard.php");
    exit();
}

$stmt = $pdo->prepare("SELECT profession, location, contact_info, bio FROM profiles WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>
<main>
    <h2>Edit Profile</h2>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="profession">Profession</label>
        <input type="text" id="profession" name="profession" value="<?php echo htmlspecialchars($profile['profession']); ?>" required>

        <label for="location">Location</label>
        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($profile['location']); ?>" required>

        <label for="contact_info">Contact Info</label>
        <input type="text" id="contact_info" name="contact_info" value="<?php echo htmlspecialchars($profile['contact_info']); ?>" required>

        <label for="bio">Bio</label>
        <textarea id="bio" name="bio"><?php echo htmlspecialchars($profile['bio']); ?></textarea>

        <button type="submit">Save Changes</button>
    </form>
</main>
<?php include 'includes/footer.php'; ?>
