<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');

// Função para listar todas as vendas
function listarTodasVendas($pdo) {
    $stmt = $pdo->query("SELECT * FROM vendas_creditos");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$vendas = listarTodasVendas($pdo);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Todas as Vendas</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="my-4">Lista de Todas as Vendas</h1>
    <table class="table table-striped table-custom">
        <thead>
            <tr>
                <th>ID</th>
                <th>Revendedor ID</th>
                <th>Servidor ID</th>
                <th>Quantidade</th>
                <th>Preço Crédito</th>
                <th>Valor Total</th>
                <th>Data Venda</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($vendas)): ?>
            <?php foreach ($vendas as $venda): ?>
                <tr>
                    <td><?= htmlspecialchars($venda['id']) ?></td>
                    <td><?= htmlspecialchars($venda['revendedor_id']) ?></td>
                    <td><?= htmlspecialchars($venda['servidor_id']) ?></td>
                    <td><?= htmlspecialchars($venda['quantidade']) ?></td>
                    <td><?= htmlspecialchars($venda['preco_credito']) ?></td>
                    <td><?= htmlspecialchars($venda['valor_total']) ?></td>
                    <td><?= htmlspecialchars($venda['data_venda']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">Nenhuma venda encontrada.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
