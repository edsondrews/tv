<?php
include('config/auth.php');
include('config/db.php');
include('config/funcoes_revendedores.php');

$servidores = listarServidores($pdo);
$revendedores = listarRevendedores($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $servidores_preco = $_POST['precos'];
    $revendedor_id = isset($_POST['revendedor_id']) ? $_POST['revendedor_id'] : null;
    $user_id = $_SESSION['user_id'];

    adicionarOuAtualizarRevendedor($pdo, $nome, $servidores_preco, $user_id, $revendedor_id);
    header('Location: revendedores.php');
    exit();
}

if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] === 'edit') {
    $revendedor = obterRevendedorPorId($pdo, $_GET['id']);
    $precos = obterPrecosRevendedor($pdo, $_GET['id']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Revendedores</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Gerenciar Revendedores</h1>

    <form method="POST">
        <input type="hidden" name="revendedor_id" value="<?php echo $revendedor['id'] ?? ''; ?>">
        <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($revendedor['nome'] ?? '', ENT_QUOTES); ?>" required>
        </div>
        <div class="form-group">
            <label>Servidores</label>
            <?php foreach ($servidores as $servidor): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="servidores[<?php echo $servidor['id']; ?>]" id="servidor-<?php echo $servidor['id']; ?>"
                        <?php
                        if (isset($precos)) {
                            foreach ($precos as $preco) {
                                if ($preco['servidor_id'] == $servidor['id']) {
                                    echo 'checked';
                                }
                            }
                        }
                        ?>>
                    <label class="form-check-label" for="servidor-<?php echo $servidor['id']; ?>">
                        <?php echo htmlspecialchars($servidor['name']); ?>
                    </label>
                    <input type="text" class="form-control mt-2" name="precos[<?php echo $servidor['id']; ?>]" placeholder="Preço" value="<?php
                    if (isset($precos)) {
                        foreach ($precos as $preco) {
                            if ($preco['servidor_id'] == $servidor['id']) {
                                echo htmlspecialchars($preco['preco']);
                            }
                        }
                    }
                    ?>">
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>

    <h2 class="mt-5">Lista de Revendedores</h2>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Nome</th>
            <th>Servidores</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($revendedores as $revendedor): ?>
            <tr>
                <td><?php echo htmlspecialchars($revendedor['nome']); ?></td>
                <td>
                    <?php if (!empty($revendedor['servidores'])): ?>
                        <ul>
                            <?php foreach ($revendedor['servidores'] as $servidor): ?>
                                <li><?php echo htmlspecialchars($servidor['name'] . ' - R$' . $servidor['preco']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        Nenhum servidor associado
                    <?php endif; ?>
                </td>
                <td>
                    <a href="revendedores.php?id=<?php echo $revendedor['id']; ?>&action=edit" class="btn btn-sm btn-warning">Editar</a>
                    <form method="POST" action="excluir_revendedor.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $revendedor['id']; ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
