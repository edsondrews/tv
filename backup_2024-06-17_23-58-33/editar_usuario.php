<?php
session_start();
require 'config/config.php'; // Ajuste para o caminho correto do arquivo de configuração do seu sistema

// Caminho do arquivo de banco de dados SQLite
$dbPath = __DIR__ . '/sistema.db';

try {
    // Conectar ao banco de dados SQLite
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Verificar se o ID do cliente foi fornecido
if (!isset($_GET['id'])) {
    die("ID do cliente não fornecido.");
}

$cliente_id = $_GET['id'];

// Obter os dados do cliente
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = :id");
$stmt->bindParam(':id', $cliente_id);
$stmt->execute();
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    die("Cliente não encontrado.");
}

// Processar o formulário de edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $usuario = $_POST['usuario'];
    $telefone = $_POST['telefone'];
    $vencimento = $_POST['vencimento'];

    $stmt = $pdo->prepare("UPDATE clientes SET nome = :nome, usuario = :usuario, telefone = :telefone, vencimento = :vencimento WHERE id = :id");
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':vencimento', $vencimento);
    $stmt->bindParam(':id', $cliente_id);

    if ($stmt->execute()) {
        // Redirecionar após atualizar o cliente
        header('Location: listar_clientes.php?editado=1');
        exit;
    } else {
        $erro = "Erro ao atualizar cliente.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="my-4">Editar Cliente</h1>
    <?php if (isset($erro)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($erro); ?>
        </div>
    <?php endif; ?>
    <form action="editar_cliente.php?id=<?php echo $cliente_id; ?>" method="POST">
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" class="form-control" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
        </div>
        <div class="form-group">
            <label for="usuario">Usuário:</label>
            <input type="text" id="usuario" name="usuario" class="form-control" value="<?php echo htmlspecialchars($cliente['usuario']); ?>" required>
        </div>
        <div class="form-group">
            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" class="form-control" value="<?php echo htmlspecialchars($cliente['telefone']); ?>" required>
        </div>
        <div class="form-group">
            <label for="vencimento">Vencimento:</label>
            <input type="date" id="vencimento" name="vencimento" class="form-control" value="<?php echo htmlspecialchars($cliente['vencimento']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="listar_clientes.php" class="btn btn-secondary">Voltar</a>
    </form>
</div>
</body>
</html>
