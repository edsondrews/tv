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

// Consultar informações do cliente e dados relacionados
$query = "
    SELECT c.*, 
           p.name AS plano_nome, 
           s.name AS servidor_nome, 
           cp.name AS captacao_nome, 
           d.name AS dispositivo_nome, 
           a.nome AS aplicativo_nome, 
           f.nome AS formas_pagamento_nome 
    FROM clientes c 
    LEFT JOIN plans p ON c.plano = p.id 
    LEFT JOIN servers s ON c.servidor = s.id 
    LEFT JOIN capture_methods cp ON c.captacao = cp.id 
    LEFT JOIN devices d ON c.dispositivo = d.id 
    LEFT JOIN aplicativos a ON c.aplicativo = a.id 
    LEFT JOIN formas_pagamento f ON c.forma_pagamento = f.id  
    WHERE c.id = :id
";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $cliente_id);
$stmt->execute();
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    die("Cliente não encontrado.");
}

function formatOutput($value) {
    return $value !== null ? htmlspecialchars($value) : 'N/A';
}

function formatDate($date) {
    return $date !== null ? date('d/m/Y', strtotime($date)) : 'N/A';
}

function formatTime($time) {
    return $time !== null ? date('H:i', strtotime($time)) : 'N/A';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Cliente</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="titulo-detalhes-cliente">Detalhes do Cliente</h2>
    <div class="detalhes-cliente">
        <h5><?php echo formatOutput($cliente['nome']); ?></h5>
        <p><strong>Usuário:</strong> <?php echo formatOutput($cliente['usuario']); ?></p>
        <p><strong>Senha:</strong> <?php echo formatOutput($cliente['senha']); ?></p>
        <p><strong>Servidor:</strong> <?php echo formatOutput($cliente['servidor_nome']); ?></p>
        <p><strong>Telefone:</strong> <?php echo formatOutput($cliente['telefone']); ?></p>
        <p><strong>Captação:</strong> <?php echo formatOutput($cliente['captacao_nome']); ?></p>
        <p><strong>Indicado por:</strong> <?php echo formatOutput($cliente['indicado_por']); ?></p>
        <p><strong>Plano:</strong> <?php echo formatOutput($cliente['plano_nome']); ?></p>
        <p><strong>Valor:</strong> <?php echo formatOutput($cliente['valor']); ?></p>
        <p><strong>Forma de Pagamento:</strong> <?php echo formatOutput($cliente['formas_pagamento_nome']); ?></p>
        <p><strong>Telas:</strong> <?php echo formatOutput($cliente['telas']); ?></p>
        <p><strong>Status:</strong> <?php echo formatOutput($cliente['status']); ?></p>
        <p><strong>Data de Criação:</strong> <?php echo formatDate($cliente['data_criacao']); ?></p>
        <p><strong>Dispositivo:</strong> <?php echo formatOutput($cliente['dispositivo_nome']); ?></p>
        <p><strong>Aplicativo:</strong> <?php echo formatOutput($cliente['aplicativo_nome']); ?></p>
        <p><strong>MAC:</strong> <?php echo formatOutput($cliente['mac']); ?></p>
        <p><strong>Device Key:</strong> <?php echo formatOutput($cliente['device_key']); ?></p>
        <p><strong>Vencimento do Aplicativo:</strong> <?php echo formatDate($cliente['vencimento_aplicativo']); ?></p>
        <p><strong>Vencimento:</strong> <?php echo formatDate($cliente['vencimento']); ?></p>
        <p><strong>Hora de Vencimento:</strong> <?php echo formatTime($cliente['hora_vencimento']); ?></p>
        <p><strong>Aniversário:</strong> <?php echo formatDate($cliente['aniversario']); ?></p>
        <p><strong>Email:</strong> <?php echo formatOutput($cliente['email']); ?></p>
        <p><strong>Observação:</strong> <?php echo formatOutput($cliente['observacao']); ?></p>
    </div>
    <div class="modal-footer">
        <a href="listar_clientes.php" class="btn btn-primary">Voltar</a>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
