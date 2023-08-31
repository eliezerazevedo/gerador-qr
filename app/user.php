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

// Excluir usuário se o parâmetro 'delete_id' for fornecido
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $delete_stmt->bind_param("i", $delete_id);
    $delete_stmt->execute();
    $delete_stmt->close();
    header('Location: user.php'); // Redirecionar de volta para a página de gerenciamento de usuários
    exit;
}

$stmt = $conn->prepare("SELECT id, username, permission FROM users");
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<?php include 'view/head.php'; ?>
<body>
<?php include 'view/menu.php'; ?>
<div class="container mt-4">
    <table class="table">
        <thead>
            <tr>
                <th>Nome de Usuário</th>
                <th>Permissão</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row["username"]; ?></td>
                    <td><?php echo $row["permission"]; ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $row["id"]; ?>" class="btn btn-primary">Editar</a>
                        <a href="user.php?delete_id=<?php echo $row["id"]; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<!-- Adicione os scripts do Bootstrap no final do arquivo -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$stmt->close();
?>
