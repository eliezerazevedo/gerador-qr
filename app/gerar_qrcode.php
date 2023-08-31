<?php
session_start();

// Redirecionar para index.php se o usuário não estiver autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit();
}

require_once '../config/config.php';

// Inicializar variáveis
$input_usuario = "";
$errorMessage = "";

// Trata o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_usuario = $_POST['conteudo'];

    // Validar inserção do usuario
    $insertQuery = "INSERT INTO input_usuario (valor_input) VALUES (?)";
    $stmt = $conn->prepare($insertQuery);

    if ($stmt) {
        $stmt->bind_param("s", $input_usuario);
        if ($stmt->execute()) {
            // Validar status de conexão e gravação no Db
        } else {
            $errorMessage = "Erro ao armazenar entrada no banco de dados.";
        }
        $stmt->close();
    } else {
        $errorMessage = "Erro ao preparar a declaração.";
    }
    $conn->close();
}

// Gere o código QR e retorne o URL
function generateQRCode($content) {
    // QR code link
    $link = "https://wa.me/556232742369?text=Ol%C3%A1%2C+preciso+de+suporte+para+meu+equipamento%2C+N%C2%BA+de+patrim%C3%B4nio%3A+$content";
    $directoryQR = 'qrcodes/';

    if (!file_exists($directoryQR)) {
        mkdir($directoryQR, 0777, true);
    }

    // Rotina de limpeza da pasta qrFiles
    $qrFiles = glob($directoryQR . '*.png');
    if (count($qrFiles) >= 3) {
        usort($qrFiles, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        for ($i = 0; $i < count($qrFiles) - 2; $i++) {
            unlink($qrFiles[$i]);
        }
    }

    $filename = uniqid() . '.png';
    $filepath = $directoryQR . $filename;
    QRcode::png($link, $filepath, QR_ECLEVEL_Q, 2);
    return $filepath;
}
?>

<!DOCTYPE html>
<html>
<?php include 'view/head_print.php'; ?>
<body>
<div class="no-print">
    <button onclick="history.back()">Voltar</button>
    <p><button id="botao-impressao">Imprimir</button></p>
</div>
<div class="container">
    <?php
    require_once 'phpqrcode/qrlib.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['conteudo'])) {
        $content = $_POST['conteudo'];
        $qrCodeFilePath = generateQRCode($content);

        echo "<div class='container'>";
        echo "<div class='qr-code-container'>";
        echo "<img class='qr-code' src='$qrCodeFilePath'>";
        echo "</div>";
        echo "<div class='qr-code-text'>";
        echo "<b>$content</b>";
        echo "</div>";
        echo "</div>";
    }
    ?>
</div>
<script>
document.getElementById("botao-impressao").addEventListener("click", function() {
    window.print();
});
</script>
</body>
</html>
