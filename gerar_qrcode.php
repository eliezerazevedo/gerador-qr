<?php
// Iniciar uma sessão ou retomar uma sessão existente
session_start();

// Se o usuário não estiver logado (a sessão não possui 'user_id' definido)
if (!isset($_SESSION['user_id'])) {
    // Redirecioná-los para a página de login
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
    <!-- Botão para voltar -->
    <button onclick="history.back()">Voltar</button>
    <!-- Botão para acionar a impressão da página -->
    <p><button id="botao-impressao">Imprimir</button></p>
</div>
<div class="container">
    <?php
    // Incluir a biblioteca de geração de códigos QR
    require_once 'phpqrcode/qrlib.php';

    // Função para gerar um código QR e retornar o caminho do arquivo
    function generateQRCode($content) {
        // Link do WhatsApp com o parâmetro de conteúdo
        $link = "https://wa.me/556232742369?text=Ol%C3%A1%2C+preciso+de+suporte+para+meu+equipamento%2C+N%C2%BA+de+patrim%C3%B4nio%3A+$content";

        $directoryQR = 'qrcodes/';

        // Criar o diretório se ele não existir
        if (!file_exists($directoryQR)) {
            mkdir($directoryQR, 0777, true);
        }

        // Gerenciar arquivos de códigos QR
        $qrFiles = glob($directoryQR . '*.png');
        if (count($qrFiles) >= 3) {
            // Remover arquivos antigos (manter os 2 mais recentes)
            usort($qrFiles, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            for ($i = 0; $i < count($qrFiles) - 2; $i++) {
                unlink($qrFiles[$i]);
            }
        }

        // Gerar um nome de arquivo único para o código QR
        $filename = uniqid() . '.png';
        $filepath = $directoryQR . $filename;

        // Gerar a imagem do código QR
        QRcode::png($link, $filepath, QR_ECLEVEL_Q, 2);

        return $filepath;
    }

    // Lidar com o envio do formulário
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['conteudo'])) {
        $content = $_POST['conteudo'];
        $qrCodeFilePath = generateQRCode($content);

        // Exibir o código QR e seu conteúdo relacionado
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
// Adicionar um ouvinte de evento ao botão de impressão
document.getElementById("botao-impressao").addEventListener("click", function() {
    window.print(); // Chamar a função de impressão do navegador
});
</script>
</body>
</html>
