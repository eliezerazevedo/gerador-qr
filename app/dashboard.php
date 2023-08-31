<?php
session_start();

// Redirecionar para index.php se o usuário não estiver autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit();
}

require_once '../config/config.php';

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

// Buscar os dez últimos valores inseridos no banco de dados
$querySelecaoUltimos = "SELECT valor_input FROM input_usuario ORDER BY ID DESC LIMIT 10";
$resultadoUltimos = $conn->query($querySelecaoUltimos);
$ultimosValores = [];

if ($resultadoUltimos && $resultadoUltimos->num_rows > 0) {
    while ($linhaUltimos = $resultadoUltimos->fetch_assoc()) {
        $ultimosValores[] = $linhaUltimos["valor_input"];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<?php include 'view/head.php'; ?>
<body>
    <?php include 'view/menu.php'; ?>
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

        <h3 class="mt-5">Últimos 10 Patrimônios/Número de Série impresso:</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Digite o Patrimônios ou Número de Série</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ultimosValores as $key => $valor): ?>
                    <tr>
                        <td><?= $key + 1; ?></td>
                        <td><?= $valor; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include 'view/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
