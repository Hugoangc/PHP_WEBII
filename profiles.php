<?php
require 'includes/db_connect.php';
include 'includes/header.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT * FROM professionals WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $profile = $result->fetch_assoc();
    } else {
        echo "Profile not found.";
        exit;
    }
} else {
    echo "No profile ID provided.";
    exit;
}
?>


<body>

<main>
        <div class="profile-container">
            <h2><?php echo htmlspecialchars($profile['name']); ?></h2>
            <p><strong>Profession:</strong> <?php echo htmlspecialchars($profile['profession']); ?></p>
            <p><strong>Service Cost:</strong> <?php echo htmlspecialchars($profile['service_cost']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($profile['location']); ?></p>
            <p><strong>Contact:</strong> <?php echo htmlspecialchars($profile['contact']); ?></p>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>

</body>
</html>