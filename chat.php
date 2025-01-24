<?php
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$sender_id = $_SESSION['user_id'];
$receiver_id = intval($_GET['id']); // ID do profissional a ser contatado

// Obter informações do receptor
$stmt = $pdo->prepare("SELECT fullname FROM users WHERE id = :receiver_id");
$stmt->execute(['receiver_id' => $receiver_id]);
$receiver = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$receiver) {
    echo "Usuário não encontrado.";
    exit;
}

// Enviar mensagem via POST (caso o JavaScript não esteja habilitado)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message_content = trim($_POST['message_content']);
    if (!empty($message_content)) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message_content) VALUES (:sender_id, :receiver_id, :message_content)");
        $stmt->execute([
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'message_content' => $message_content
        ]);

        // Redirecionar para evitar reenvio do formulário
        header("Location: chat.php?id=$receiver_id");
        exit;
    }
}
?>

<main>
    <h2>Chat com <?php echo htmlspecialchars($receiver['fullname']); ?></h2>
    <div class="chat-box" style="border: 1px solid #ccc; padding: 100px; height: 400px; overflow-y: auto;">
    </div>

    <form id="chatForm" method="POST" action="">
        <textarea name="message_content" rows="3" required></textarea>
        <button type="submit">Enviar</button>
    </form>
</main>

<script>
    const chatBox = document.querySelector('.chat-box');
    const receiverId = <?php echo $receiver_id; ?>; // ID do receptor
    const senderId = <?php echo $sender_id; ?>; // ID do remetente

    // buscar e atualizar as mensagens
    function fetchMessages() {
        fetch(`fetch_messages.php?id=${receiverId}`)
            .then(response => response.json())
            .then(messages => {
                chatBox.innerHTML = ''; // Limpar o chat 

                // Renderizar as mensagens
                messages.forEach(message => {
                    const messageDiv = document.createElement('div');
                    messageDiv.classList.add(message.sender_id == senderId ? 'message-sent' : 'message-received');
                    messageDiv.innerHTML = `
                        <p><strong>${message.sender_name}:</strong></p>
                        <p>${message.message_content}</p>
                        <span class="timestamp">${message.timestamp}</span>
                    `;
                    chatBox.appendChild(messageDiv);
                });

                // Rolagem automática para a última mensagem
                chatBox.scrollTop = chatBox.scrollHeight;
            })
            .catch(error => console.error('Erro ao buscar mensagens:', error));
    }

    setInterval(fetchMessages, 2000);

    fetchMessages();

    // Mensagem via AJAX
    const chatForm = document.getElementById('chatForm');
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Impedir recarregamento da página

        const formData = new FormData(chatForm);

        fetch('', {
            method: 'POST',
            body: formData
        }).then(response => {
            if (response.ok) {
                chatForm.reset(); // Limpar o campo de texto
                fetchMessages(); // Atualizar mensagens
            } else {
                console.error('Erro ao enviar mensagem');
            }
        });
    });
</script>
<style>
    .message-sent {
        text-align: right;
        background-color: #d1ffd6;
        padding: 10px;
        margin: 5px;
        border-radius: 10px;
    }

    .message-received {
        text-align: left;
        background-color: #f1f1f1;
        padding: 10px;
        margin: 5px;
        border-radius: 10px;
    }

    .timestamp {
        display: block;
        font-size: 0.8em;
        color: #777;
    }
</style>

<?php include 'includes/footer.php'; ?>