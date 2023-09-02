<?php
session_start();

// Redirecionar para index.php se o usuário não estiver autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit();
}

require_once '../config/config.php';

// Inicializar variáveis
$input_usuario_ind = "";
$errorMessage = "";

// Trata o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_usuario_ind = $_POST['conteudo'];

    // Validar inserção do usuario
    $insertQuery = "INSERT INTO input_usuario_ind (valor_input) VALUES (?)";
    $stmt = $conn->prepare($insertQuery);

    if ($stmt) {
        $stmt->bind_param("s", $input_usuario_ind);
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
function generateQRCode($content, $content2, $content3) {
    // QR code link
    $link = "https://wa.me/556232742369?text=Preciso+de+suporte+para+meu+equipamento%3A+$content2+%28$content3%29+N%C2%BA+de+patrim%C3%B4nio%3A+$content";
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
<?php include 'view/head_print_ind.php'; ?>
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
        $content2 = $_POST['conteudo2'];
        $content3 = $_POST['conteudo3'];
        $qrCodeFilePath = generateQRCode($content, $content2, $content3);

        echo "<div class='container'>";
        echo "<div class='qr-code-container'>";
        echo "<img class='qr-code' src='$qrCodeFilePath'>";
        echo "</div>";
        echo "<div class='qr-code-text'>";
        echo "<b>$content</b>";
        echo "</div>";
        echo "<div class='qr-code-text-2'>";
        echo "<b>$content2 - $content3</b>";
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
