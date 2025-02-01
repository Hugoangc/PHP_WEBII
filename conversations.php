<?php
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Obter conversas do usuário logado
$stmt = $pdo->prepare("
    SELECT 
        u.id AS user_id,
        u.fullname AS user_name,
        m.message_content,
        m.timestamp
    FROM messages m
    JOIN users u ON 
        (u.id = m.sender_id AND m.receiver_id = :user_id)
        OR (u.id = m.receiver_id AND m.sender_id = :user_id)
    WHERE m.id IN (
        SELECT MAX(m2.id)
        FROM messages m2
        WHERE (m2.sender_id = :user_id OR m2.receiver_id = :user_id)
        GROUP BY LEAST(m2.sender_id, m2.receiver_id), GREATEST(m2.sender_id, m2.receiver_id)
    )
    ORDER BY m.timestamp DESC
");
$stmt->execute(['user_id' => $user_id]);
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="assets/css/conversation.css">
<main>
    <h2>Minhas Conversas</h2>
    <div class="conversation-list">
        <?php if (count($conversations) > 0): ?>
            <?php foreach ($conversations as $conversation): ?>
                <div class="conversation-item">
                    <a href="chat.php?id=<?php echo $conversation['user_id']; ?>">
                        <h3><?php echo htmlspecialchars($conversation['user_name']); ?></h3>
                        <p><?php echo htmlspecialchars($conversation['message_content']); ?></p>
                        <span class="timestamp"><?php echo htmlspecialchars($conversation['timestamp']); ?></span>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Você ainda não tem conversas ativas.</p>
        <?php endif; ?>
    </div>
    <button onclick="window.history.back();" class="btn-secondary">Voltar</button>
</main>

<?php include 'includes/footer.php'; ?>