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
    <meta charset="UTF-8">
    <title>Gerador de QR Code</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'menu.php'; ?>
    <div class="container">
        <h1 class="mt-5">Gerador de QR Code</h1>
        <?php if (!empty($errorMessage)) { ?>
            <p class="text-danger"><?php echo $errorMessage; ?></p>
        <?php } ?>
        <form action="gerar_qrcode.php" method="post" class="mt-3">
            <div class="form-group">
                <label for="conteudo">Informe o conteúdo:</label>
                <input type="text" name="conteudo" id="conteudo" class="form-control" placeholder="" required>
            </div>
            <button type="submit" class="btn btn-primary">Gerar QR Code</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
