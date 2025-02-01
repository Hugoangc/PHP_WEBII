<?php include 'includes/header.php';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);


    $profession = ucwords(strtolower(trim($_POST['profession'])));
    $location = $_POST['location'];
    $contact_info = $_POST['contact'];
    $service_name = $_POST['service_name'];
    $service_description = $_POST['service_description'];
    $service_price = $_POST['service_price'];

    try {
        $pdo->beginTransaction();

        $stmt_user = $pdo->prepare("INSERT INTO users (fullname, email, password, role) VALUES (:fullname, :email, :password, 'professional')");
        $stmt_user->execute(['fullname' => $fullname, 'email' => $email, 'password' => $password]);
        $user_id = $pdo->lastInsertId();

        $stmt_profile = $pdo->prepare("INSERT INTO profiles (user_id, profession, location, contact_info) VALUES (:user_id, :profession, :location, :contact_info)");
        $stmt_profile->execute([
            'user_id' => $user_id,
            'profession' => $profession,
            'location' => $location,
            'contact_info' => $contact_info
        ]);
        $profile_id = $pdo->lastInsertId();

        $stmt_service = $pdo->prepare("INSERT INTO services (profile_id, service_name, description, price) VALUES (:profile_id, :service_name, :description, :price)");
        $stmt_service->execute([
            'profile_id' => $profile_id,
            'service_name' => $service_name,
            'description' => $service_description,
            'price' => $service_price
        ]);

        $pdo->commit();
        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}?>
<main>
    <h2>Register</h2>
    <form method="POST" action="">
        <label for="fullname">Nome Completo</label>
        <input type="text" id="fullname" name="fullname" required>

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Senha</label>
        <input type="password" id="password" name="password" required>

        <label for="profession">Profissão</label>
        <input type="text" id="profession" name="profession" required>

        <label for="location">Localização</label>
        <input type="text" id="location" name="location" required>

        <label for="contact">Informação de contato</label>
        <input type="text" id="contact" name="contact" required>

        <label for="service_name">Nome do Serviço</label>
        <input type="text" id="service_name" name="service_name" required>

        <label for="service_description">Descrição do serviço</label>
        <textarea id="service_description" name="service_description"></textarea>

        <label for="service_price">Serviço/hora</label>
        <input type="number" id="service_price" name="service_price" step="1.00" required>

        <button type="submit">Register</button>
    </form>

    <a href="login.php">Já possui uma conta?</a>
</main>
<?php include 'includes/footer.php'; ?>
