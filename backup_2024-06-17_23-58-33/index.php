<?php
include('config/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Credenciais inválidas";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="login-container">
            <h1 class="mb-4 text-center">Bem-vindo ao Sistema de Gestão</h1>
            <p class="welcome-message text-center">Faça login para acessar seu painel e gerenciar suas tarefas de forma eficiente e organizada.</p>
            <form method="POST">
                <div class="form-group">
                    <input type="text" name="username" class="form-control form-control-lg" placeholder="Nome de usuário" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Senha" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
                <?php if (isset($error)) echo "<div class='alert alert-danger mt-3'>$error</div>"; ?>
            </form>
        </div>
    </div>
</body>
</html>
