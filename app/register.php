<!DOCTYPE html>
<html>
<?php include 'view/head.php'; ?>
<body>
<?php include 'view/menu.php'; ?>
<div class="container mt-5">
    <div class="col-md-6 offset-md-3">
        <h2 class="mb-4">Cadastrar Usuário</h2>
        <?php
        require_once '../config/config.php';
        // Iniciar a sessão
session_start();

// Verificar se o usuário está autenticado e tem permissão "master"
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirecionar para a página de login se não estiver autenticado
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT permission FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user['permission'] !== 'master') {
    header('Location: access_denied.php'); // Redirecionar se não tiver permissão de "master"
    exit;
}

$stmt = $conn->prepare("SELECT id, username, permission FROM users");
$stmt->execute();
$result = $stmt->get_result();

        function displayAlert($message, $alertType) {
            echo '<div class="alert ' . $alertType . '">' . $message . '</div>';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $permission = $_POST['permission'];

            if (empty($username) || empty($password)) {
                displayAlert('Preencha todos os campos obrigatórios.', 'alert-danger');
            } else {
                $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                $existingUser = $result->fetch_assoc();

                if ($existingUser) {
                    displayAlert('Nome de usuário já existe. Escolha outro.', 'alert-danger');
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    $stmt = $conn->prepare("INSERT INTO users (username, password, permission) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $username, $hashedPassword, $permission);
                    
                    if ($stmt->execute()) {
                        displayAlert('Usuário registrado com sucesso!', 'alert-success');
                    } else {
                        displayAlert('Erro ao registrar usuário. Por favor, tente novamente.', 'alert-danger');
                    }
                }
            }
        }

        if ($stmt) {
            $stmt->close(); // Fechar a declaração, se estiver definida
        }
        ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">Usuário:</label>
                <input type="text" class="form-control" id="username" name="username">
            </div>
            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="permission">Nível de Permissão:</label>
                <select class="form-control" id="permission" name="permission">
                    <option value="comum">Comum</option>
                    <option value="master">Master</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Registrar</button>
        </form>
    </div>
</div>

<!-- Incluir o JavaScript do Bootstrap (opcional) -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
