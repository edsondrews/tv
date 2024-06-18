<?php
// Incluir a configuração do banco de dados
include('config/db.php');

// Dados do usuário master
$username = 'admin';
$password = password_hash('senha_secreta', PASSWORD_BCRYPT); // Criptografa a senha para segurança
$role = 'master';

// Verificar se o usuário master já existe
$query = "SELECT * FROM users WHERE username = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user) {
    echo "Usuário master já existe.";
} else {
    // Inserir o usuário master no banco de dados
    $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username, $password, $role]);

    if ($stmt) {
        echo "Usuário master criado com sucesso.";
    } else {
        echo "Erro ao criar usuário master.";
    }
}
?>
