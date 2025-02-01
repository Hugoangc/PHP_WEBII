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
    /* Estilo geral */
body {
  font-family: Arial, sans-serif;
  background-color: #f4f4f4;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  height: 100vh;
}

main {
  flex: 1;
  display: flex;
  flex-direction: column;
  width: 100%;
  margin: 20px auto;
  padding: 20px;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #333;
}

/* Estilo da lista de mensagens */
.message-list {
  flex: 1;
  overflow-y: auto;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background-color: #fafafa;
}

.message-sent {
  text-align: right;
  background-color: #d1ffd6;
  padding: 10px;
  margin: 5px;
  border-radius: 10px;
  max-width: 70%;
  align-self: flex-end;
}

.message-received {
  text-align: left;
  background-color: #f1f1f1;
  padding: 10px;
  margin: 5px;
  border-radius: 10px;
  max-width: 70%;
  align-self: flex-start;
}

.timestamp {
  display: block;
  font-size: 0.8em;
  color: #777;
  margin-top: 5px;
}

/* Estilo do formulário de envio */
.chat-form {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.chat-form input[type="text"] {
  flex: 1;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.chat-form input[type="text"]:focus {
  border-color: #269199;
  box-shadow: 0 0 5px rgba(38, 145, 153, 0.5);
  outline: none;
}

.chat-form button {
  background-color: #269199;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1em;
  transition: background-color 0.3s ease;
}

.chat-form button:hover {
  background-color: #1e7a8c;
}

.chat-form button:active {
  background-color: #155d6e;
}

.chat-form button:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(72, 187, 120, 0.5);
}
</style>

<?php include 'includes/footer.php'; ?>