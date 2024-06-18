<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Processar a venda de créditos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $revendedor_id = $_POST['revendedor_id'];
    $servidor_id = $_POST['servidor_id'];
    $quantidade = (int)$_POST['quantidade'];

    // Obter o preço do crédito no momento da venda
    $stmt = $pdo->prepare("SELECT preco FROM precos_revendedores WHERE revendedor_id = ? AND servidor_id = ?");
    $stmt->execute([$revendedor_id, $servidor_id]);
    $preco_credito = (float)$stmt->fetchColumn();

    if ($preco_credito === false) {
        $error = 'Preço do crédito não encontrado.';
    } else {
        $valor_total = $quantidade * $preco_credito;

        $stmt = $pdo->prepare("INSERT INTO vendas_creditos (revendedor_id, servidor_id, quantidade, preco_credito, valor_total) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$revendedor_id, $servidor_id, $quantidade, $preco_credito, $valor_total]);

        $message = 'Venda registrada com sucesso!';
    }
}

// Obter lista de revendedores
$stmt = $pdo->prepare("SELECT * FROM revendedores WHERE user_id = ?");
$stmt->execute([$user_id]);
$revendedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obter lista de servidores
$stmt = $pdo->prepare("SELECT * FROM servers WHERE user_id = ?");
$stmt->execute([$user_id]);
$servidores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Venda de Créditos</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .container {
            max-width: 800px;
            margin-top: 50px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="my-4">Venda de Créditos</h1>

    <?php if (isset($message)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="vendas_creditos.php">
        <div class="form-group">
            <label for="revendedor_id">Revendedor:</label>
            <select id="revendedor_id" name="revendedor_id" class="form-control" required>
                <option value="" disabled selected>Selecione um revendedor</option>
                <?php foreach ($revendedores as $revendedor): ?>
                    <option value="<?php echo $revendedor['id']; ?>"><?php echo htmlspecialchars($revendedor['nome']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="servidor_id">Servidor:</label>
            <select id="servidor_id" name="servidor_id" class="form-control" required>
                <option value="" disabled selected>Selecione um servidor</option>
                <?php foreach ($servidores as $servidor): ?>
                    <option value="<?php echo $servidor['id']; ?>"><?php echo htmlspecialchars($servidor['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="quantidade">Quantidade de Créditos:</label>
            <input type="number" id="quantidade" name="quantidade" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrar Venda</button>
    </form>

    <a href="dashboard.php" class="btn btn-secondary mt-3">Voltar</a>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
