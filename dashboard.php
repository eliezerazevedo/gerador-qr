<?php
session_start();

// Redirecionar para index.php se o usuário não estiver autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config.php';

// Inicializar variáveis
$mensagemErro = "";
$ultimoInput = "";

// Buscar o último valor inserido no banco de dados
$querySelecao = "SELECT valor_input FROM input_usuario ORDER BY ID DESC LIMIT 1";
$resultado = $conn->query($querySelecao);

if ($resultado && $resultado->num_rows > 0) {
    $linha = $resultado->fetch_assoc();
    $ultimoInput = $linha["valor_input"];
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gerador de QR Code</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'menu.php'; ?>
    <div class="container">
        <h1 class="mt-5">Gerador de QR Code</h1>
        <?php if (!empty($mensagemErro)): ?>
            <p class="text-danger"><?= $mensagemErro; ?></p>
        <?php endif; ?>
        <form action="gerar_qrcode.php" method="post" class="mt-3">
            <div class="form-group">
                <label for="conteudo">Digite o conteúdo:</label>
                <input type="text" name="conteudo" id="conteudo" class="form-control" placeholder="<?= $ultimoInput; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Gerar QR Code</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
