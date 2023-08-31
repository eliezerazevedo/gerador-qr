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

// Função para exibir alertas
function displayAlert($message, $alertType) {
    echo '<div class="alert ' . $alertType . '">' . $message . '</div>';
}

$updateSuccessMessage = '';
$updateErrorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $username = trim($_POST['username']);
    $permission = $_POST['permission'];
    $password = $_POST['password'];

    // Verificar se campos obrigatórios estão preenchidos
    if (empty($username) || empty($permission)) {
        $updateErrorMessage = 'Por favor, preencha todos os campos.';
    } else {
        // Preparar a instrução SQL para atualização (incluindo senha, se fornecida)
        $stmt = $conn->prepare("UPDATE users SET username = ?, permission = ?" . 
                              (!empty($password) ? ", password = ?" : "") . " WHERE id = ?");
        
        if (!empty($password)) {
            // Hash da senha se uma nova senha foi fornecida
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("sssi", $username, $permission, $hashedPassword, $id);
        } else {
            // Sem hash da senha se nenhuma senha nova foi fornecida
            $stmt->bind_param("ssi", $username, $permission, $id);
        }

        if ($stmt->execute()) {
            $updateSuccessMessage = 'Usuário atualizado com sucesso!';
        } else {
            displayAlert('Erro ao atualizar usuário. Por favor, tente novamente.', 'alert-danger');
        }

        $stmt->close();
    }
}

$user = ["id" => "", "username" => "", "permission" => ""];

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Recuperar informações do usuário para edição
    $stmt = $conn->prepare("SELECT id, username, permission FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

?>

<!DOCTYPE html>
<html>
<head>
    <?php include 'view/head.php'; ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'view/menu.php'; ?>
    <div class="container mt-4">
        <h1>Editar Usuário</h1>
        <?php if ($updateSuccessMessage) : ?>
            <div class="alert alert-success"><?= $updateSuccessMessage ?></div>
        <?php elseif ($updateErrorMessage) : ?>
            <div class="alert alert-danger"><?= $updateErrorMessage ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
            <div class="form-group">
                <label for="username">Nome de Usuário:</label>
                <input type="text" id="username" name="username" class="form-control" value="<?= $user['username'] ?>">
            </div>
            <div class="form-group">
                <label for="permission">Nível de Permissão:</label>
                <select class="form-control" id="permission" name="permission">
                    <option value="comum" <?= ($user['permission'] === 'comum') ? 'selected' : '' ?>>Comum</option>
                    <option value="master" <?= ($user['permission'] === 'master') ? 'selected' : '' ?>>Master</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Nova Senha:</label>
                <input type="password" id="password" name="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="/app/user.php" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
