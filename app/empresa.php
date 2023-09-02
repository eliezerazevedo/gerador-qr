<!DOCTYPE html>
<html>
<?php include 'view/head.php'; ?>
<body>
<?php include 'view/menu.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1>Formulário de Empresa</h1>

            <?php
            // Processar o formulário quando for enviado
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Incluir o arquivo de configuração
                require_once '../config/config.php';

                // Iniciar a sessão
                session_start();

                // Verificar se o usuário está autenticado
                if (!isset($_SESSION['user_id'])) {
                    header('Location: login.php'); // Redirecionar para a página de login se não estiver autenticado
                    exit;
                }

                // Verificar se o usuário tem permissão "master"
                $stmt = $conn->prepare("SELECT permission FROM users WHERE id = ?");
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $stmt->close();

                if ($user['permission'] !== 'master') {
                    header('Location: access_denied.php'); // Redirecionar se não tiver permissão de "master"
                    exit;
                }

                // Obter dados do formulário
                $nome = $_POST['nome'];
                $cnpj = $_POST['cnpj']; // Alteração do nome do campo

                // Inserir dados na tabela
                $sql = "INSERT INTO empresa (nome, cnpj) VALUES (?, ?)"; // Alteração do nome do campo na query SQL
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $nome, $cnpj); // Alteração do nome do campo no bind_param

                if ($stmt->execute()) {
                    echo '<div class="alert alert-success mt-3">Dados inseridos com sucesso!</div>';
                } else {
                    echo '<div class="alert alert-danger mt-3">Erro ao inserir dados: ' . $stmt->error . '</div>';
                }

                // Fechar a conexão
                $stmt->close();
                $conn->close();
            }
            ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome:</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="cnpj" class="form-label">CNPJ:</label>
                    <input type="text" name="cnpj" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Cadastrar</button>
            </form>
        </div>
    </div>
</div>
<?php include 'view/footer.php'; ?>
</body>
</html>
