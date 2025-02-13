<?php
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Inicia a transação
        $pdo->beginTransaction();

        // Captura os dados do formulário
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = trim($_POST['role']);

        // Insere usuário na tabela users
        $sql = "INSERT INTO users (fullname, email, password, role, created_at) VALUES (:fullname, :email, :password, :role, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':fullname' => $fullname,
            ':email' => $email,
            ':password' => $password,
            ':role' => $role
        ]);

        $user_id = $pdo->lastInsertId();

        // Captura os dados do perfil (incluindo valores em branco para usuários comuns)
        $profession = isset($_POST['profession']) ? trim($_POST['profession']) : '';
        $location = isset($_POST['location']) ? trim($_POST['location']) : '';
        $contact_info = isset($_POST['contact']) ? trim($_POST['contact']) : '';

        // Insere perfil na tabela profiles (sempre insere, independentemente do tipo de usuário)
        $sql = "INSERT INTO profiles (user_id, profession, location, contact_info) VALUES (:user_id, :profession, :location, :contact_info)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':profession' => $profession,
            ':location' => $location,
            ':contact_info' => $contact_info
        ]);

        // Se o usuário for profissional, pode adicionar serviço
        if ($role === 'professional' && !empty($_POST['service_name']) && !empty($_POST['service_description']) && !empty($_POST['service_price'])) {
            $profile_id = $pdo->lastInsertId();

            $service_name = trim($_POST['service_name']);
            $service_description = trim($_POST['service_description']);
            $service_price = floatval($_POST['service_price']);

            // Insere serviço na tabela services
            $sql = "INSERT INTO services (profile_id, service_name, description, price, created_at) VALUES (:profile_id, :service_name, :service_description, :service_price, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':profile_id' => $profile_id,
                ':service_name' => $service_name,
                ':service_description' => $service_description,
                ':service_price' => $service_price
            ]);
        }

        // Confirma a transação
        $pdo->commit();
        header("Location: login.php");
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Erro ao cadastrar: " . $e->getMessage());
    }
}

?>

<main>
    <h2>Cadastro</h2>
    <form method="POST" action="">
        <label for="fullname">Nome Completo</label>
        <input type="text" id="fullname" name="fullname" required>

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Senha</label>
        <input type="password" id="password" name="password" required>

        <label for="role">Tipo de Conta</label>
        <select id="role" name="role" required onchange="toggleFields()">
            <option value="user">Usuário</option>
            <option value="professional">Profissional</option>
        </select>

        <!-- Novo campo secreto para admins -->
        <input type="hidden" name="admin_key" value="CEFET">
        <!-- Campos profissionais -->
        <div id="professionalFields" style="display: none;">
            <label for="profession">Profissão</label>
            <select id="profession" name="profession">
                <option value="">Selecione uma área</option>
                <option value="Saúde">Saúde</option>
                <option value="Educação">Educação</option>
                <option value="Tecnologia">Tecnologia</option>
                <option value="Construção Civil">Construção Civil</option>
                <option value="Beleza e Estética">Beleza e Estética</option>
                <option value="Transportes">Transportes</option>
                <option value="Serviços Gerais">Serviços Gerais</option>
                <option value="Consultoria e Negócios">Consultoria e Negócios</option>
                <option value="Manutenção e Reparos">Manutenção e Reparos</option>
                <option value="Artes e Design">Artes e Design</option>
                <option value="Marketing e Publicidade">Marketing e Publicidade</option>
                <option value="Gastronomia e Alimentação">Gastronomia e Alimentação</option>
                <option value="Agronegócio e Meio Ambiente">Agronegócio e Meio Ambiente</option>
                <option value="Turismo e Hotelaria">Turismo e Hotelaria</option>
                <option value="Direito e Advocacia">Direito e Advocacia</option>
                <option value="Engenharia e Arquitetura">Engenharia e Arquitetura</option>
                <option value="Esportes e Bem-estar">Esportes e Bem-estar</option>
                <option value="Eventos e Entretenimento">Eventos e Entretenimento</option>
                <option value="Indústria e Produção">Indústria e Produção</option>
                <option value="Ciência e Pesquisa">Ciência e Pesquisa</option>
                <option value="Recursos Humanos">Recursos Humanos</option>
                <option value="Segurança e Vigilância">Segurança e Vigilância</option>
                <option value="Imobiliário">Imobiliário</option>
                <option value="Outros">Outros</option>
            </select>

            <label for="city">Cidade</label>
            <input type="text" id="city" name="city" oninput="updateLocation()">

            <label for="state">Estado</label>
            <select id="state" name="state" onchange="updateLocation()">
                <option value="">Selecione um estado</option>
                <option value="AC">Acre (AC)</option>
                <option value="AL">Alagoas (AL)</option>
                <option value="AP">Amapá (AP)</option>
                <option value="AM">Amazonas (AM)</option>
                <option value="BA">Bahia (BA)</option>
                <option value="CE">Ceará (CE)</option>
                <option value="DF">Distrito Federal (DF)</option>
                <option value="ES">Espírito Santo (ES)</option>
                <option value="GO">Goiás (GO)</option>
                <option value="MA">Maranhão (MA)</option>
                <option value="MT">Mato Grosso (MT)</option>
                <option value="MS">Mato Grosso do Sul (MS)</option>
                <option value="MG">Minas Gerais (MG)</option>
                <option value="PA">Pará (PA)</option>
                <option value="PB">Paraíba (PB)</option>
                <option value="PR">Paraná (PR)</option>
                <option value="PE">Pernambuco (PE)</option>
                <option value="PI">Piauí (PI)</option>
                <option value="RJ">Rio de Janeiro (RJ)</option>
                <option value="RN">Rio Grande do Norte (RN)</option>
                <option value="RS">Rio Grande do Sul (RS)</option>
                <option value="RO">Rondônia (RO)</option>
                <option value="RR">Roraima (RR)</option>
                <option value="SC">Santa Catarina (SC)</option>
                <option value="SP">São Paulo (SP)</option>
                <option value="SE">Sergipe (SE)</option>
                <option value="TO">Tocantins (TO)</option>
            </select>

            <label for="location">Localização</label>
            <input type="text" id="location" name="location" readonly>

            <label for="contact">Informação de contato</label>
            <input type="text" id="contact" name="contact">

            <label for="service_name">Nome do Serviço</label>
            <input type="text" id="service_name" name="service_name">

            <label for="service_description">Descrição do serviço</label>
            <textarea id="service_description" name="service_description"></textarea>

            <label for="service_price">Preço do Serviço</label>
            <input type="number" id="service_price" name="service_price" step="1.00">
        </div>

        <button type="submit">Cadastrar</button>
        </form>

    <a href="login.php">Já possui uma conta?</a>
</main>

<script>
function updateLocation() {
    var city = document.getElementById("city").value;
    var state = document.getElementById("state").value;
    
    if (city && state) {
        document.getElementById("location").value = city + " - " + state;
    } else {
        document.getElementById("location").value = "";
    }
}

function toggleFields() {
    var role = document.getElementById("role").value;
    var fields = document.getElementById("professionalFields");
    
    if (role === "professional") {
        fields.style.display = "block";
    } else {
        fields.style.display = "none";
    }
}
</script>

<?php include 'includes/footer.php'; ?>
