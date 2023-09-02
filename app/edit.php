<?php
require_once '../config/config.php';

// Iniciar a sessão
session_start();

// Função para exibir alertas
function displayAlert($message, $alertType) {
    echo '<div class="alert ' . $alertType . '">' . $message . '</div>';
}

// Verificar se o usuário está autenticado e tem permissão "master"
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirecionar para a página de login se não estiver autenticado
    exit;
}

$user_id = $_SESSION['user_id'];

// Consultar informações do usuário e empresa
$sql = "SELECT u.id, u.username, u.permission, u.empresa AS user_empresa, e.nome AS empresa_nome
        FROM users u
        LEFT JOIN empresa e ON u.empresa = e.id
        WHERE u.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    // O usuário não foi encontrado ou não pertence a uma empresa
    header('Location: access_denied.php');
    exit;
}

$updateSuccessMessage = '';
$updateErrorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $username = trim($_POST['username']);
    $permission = $_POST['permission'];
    $password = $_POST['password'];
    $newCompanyId = $_POST['empresa']; // Novo valor da empresa selecionada

    // Verificar se campos obrigatórios estão preenchidos
    if (empty($username) || empty($permission)) {
        $updateErrorMessage = 'Por favor, preencha todos os campos.';
    } else {
        // Preparar a instrução SQL para atualização
        $sql = "UPDATE users SET username = ?, permission = ?, empresa = ?";
        $params = [$username, $permission, $newCompanyId];

        // Adicionar senha à consulta apenas se uma nova senha for fornecida
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password = ?";
            $params[] = $hashedPassword;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $conn->prepare($sql);

        // Associe os parâmetros à instrução
        $paramTypes = str_repeat('s', count($params)); // Determina o tipo de cada parâmetro
        $stmt->bind_param($paramTypes, ...$params);

        if ($stmt->execute()) {
            $updateSuccessMessage = 'Usuário atualizado com sucesso!';
            
            // Atualize a variável $user para refletir a empresa atualizada
            $user['user_empresa'] = $newCompanyId;
        } else {
            displayAlert('Erro ao atualizar usuário. Por favor, tente novamente.', 'alert-danger');
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php include 'view/head.php'; ?>
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
                <label for="empresa">Empresa:</label>
                <select class="form-control" id="empresa" name="empresa">
                    <option value="">Selecione uma empresa</option>
                    <?php
                    $stmt_empresa = $conn->prepare("SELECT id, nome FROM empresa");
                    $stmt_empresa->execute();
                    $result_empresa = $stmt_empresa->get_result();
                    while ($empresa = $result_empresa->fetch_assoc()) {
                        $selected = ($empresa['id'] == $user['user_empresa']) ? 'selected' : '';
                        echo '<option value="' . $empresa['id'] . '" ' . $selected . '>' . $empresa['nome'] . '</option>';
                    }
                    $stmt_empresa->close();
                    ?>
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
