<?php
// Configura o tempo de vida da sessão para 1 hora (3600 segundos)
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
session_start();
if(isset($_SESSION['user_id'])) {
    header("Location: ./app/dashboard.php");
    exit();
}
require_once('./config/config.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Por favor, preencha todos os campos.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);  // Bind o parâmetro usando "s" para string
        $stmt->execute();  // Execute sem argumentos
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: ./app/dashboard.php");
            exit();
        } else {
            $error = "Credenciais inválidas.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<?php include './app/view/head.php'; ?>
<body>
    <div class="container mt-5">
        <div class="col-md-6 offset-md-3">
            <h2 class="mb-4">Login</h2>
            <?php if(!empty($error)) echo '<div class="alert alert-danger">' . $error . '</div>'; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
    
    <!-- Inclua o JavaScript do Bootstrap (opcional) -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
