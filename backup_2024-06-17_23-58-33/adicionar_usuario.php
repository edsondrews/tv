<?php
include('config/auth.php');
include('config/db.php');

if ($_SESSION['user_role'] !== 'master') {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $password, $role]);
        $message = "Usuário adicionado com sucesso.";
    } catch (PDOException $e) {
        $message = "Erro ao adicionar usuário: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Novo Usuário</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <h1>Adicionar Novo Usuário</h1>
    <?php if (isset($message)) echo "<p>$message</p>"; ?>
    <form method="POST">
        <label for="username">Nome de Usuário:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required>
        
        <label for="role">Tipo de Usuário:</label>
        <select id="role" name="role" required>
            <option value="admin">Admin</option>
            <option value="master">Master</option>
        </select>
        
        <button type="submit">Adicionar Usuário</button>
    </form>
    <a href="dashboard.php">Voltar ao Dashboard</a>
    <a href="dashboard.php?logout=true">Logout</a>
</body>
</html>
