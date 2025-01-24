<?php
require 'includes/db_connect.php';
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Usuário não autenticado.']);
    exit;
}

$sender_id = $_SESSION['user_id'];
$receiver_id = intval($_GET['id']); // ID do receptor

try {
    // Obter mensagens
    $stmt = $pdo->prepare("
        SELECT m.*, u.fullname AS sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE (m.sender_id = :sender_id AND m.receiver_id = :receiver_id)
           OR (m.sender_id = :receiver_id AND m.receiver_id = :sender_id)
        ORDER BY m.timestamp ASC
    ");
    $stmt->execute([
        'sender_id' => $sender_id,
        'receiver_id' => $receiver_id
    ]);

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retorna as mensagens em JSON
    header('Content-Type: application/json'); // Define o cabeçalho para JSON
    echo json_encode($messages);

} catch (PDOException $e) {
    // Em caso de erro, retorne um erro interno do servidor
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar mensagens: ' . $e->getMessage()]);
    exit;
}
