<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<div class="no-print">   
    <button onclick="history.back()">Voltar</button>
</div>
<div class="container">
    <?php
    require_once 'phpqrcode/qrlib.php';

    function generateQRCode($content) {
        // Código de geração de QR code permanece o mesmo
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['conteudo'])) {
        $contents = $_POST['conteudo'];

        foreach ($contents as $content) {
            $qrCodeFilePath = generateQRCode($content);

            echo "<div class='qr-code-container'>";
            echo "<img class='qr-code' src='$qrCodeFilePath'>";
            echo "<div class='qr-code-text'>";
            echo "<b>$content</b>";
            echo "</div>";
            echo "</div>";
        }
    }
    ?>
</div>
</body>
</html>
