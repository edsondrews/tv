<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');

// Variáveis de feedback para o usuário
$aplicativo_editado = false;
$aplicativo_excluido = false;
$erro = '';

// Processar ações de formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['excluir'])) {
        $aplicativo_id = $_POST['aplicativo_id'];
        excluirAplicativo($pdo, $aplicativo_id);
        header('Location: listar_aplicativos.php?excluido=1');
        exit();
    } elseif (isset($_POST['editar'])) {
        $aplicativo_id = $_POST['aplicativo_id'];
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'];
        editarAplicativo($pdo, $aplicativo_id, $nome, $descricao);
        header('Location: listar_aplicativos.php?editado=1');
        exit();
    }
}

$aplicativos = listarAplicativos($pdo, $_SESSION['user_id']);

// Verificar se um aplicativo foi editado
if (isset($_GET['editado'])) {
    $aplicativo_editado = true;
}

// Verificar se um aplicativo foi excluído
if (isset($_GET['excluido'])) {
    $aplicativo_excluido = true;
}

$aplicativo_para_editar = null;
if (isset($_GET['editar_id'])) {
    $aplicativo_para_editar = buscarAplicativoPorId($pdo, $_GET['editar_id']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Listar Aplicativos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Aplicativos</h1>
        <?php if ($aplicativo_editado): ?>
            <div class="alert alert-success" role="alert">
                Aplicativo editado com sucesso!
            </div>
        <?php elseif ($aplicativo_excluido): ?>
            <div class="alert alert-success" role="alert">
                Aplicativo excluído com sucesso!
            </div>
        <?php elseif ($erro): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <a href="adicionar_aplicativo.php" class="btn btn-primary mb-3">Adicionar Novo Aplicativo</a>
        <ul class="list-group mb-3">
            <?php foreach ($aplicativos as $aplicativo): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?php echo htmlspecialchars($aplicativo['nome']); ?></strong> - <?php echo htmlspecialchars($aplicativo['descricao']); ?>
                    </div>
                    <div>
                        <a href="listar_aplicativos.php?editar_id=<?php echo $aplicativo['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <form method="post" style="display:inline;" class="form-excluir-aplicativo">
                            <input type="hidden" name="aplicativo_id" value="<?php echo $aplicativo['id']; ?>">
                            <button type="submit" name="excluir" class="btn btn-sm btn-danger" onclick="return confirm('Você tem certeza que deseja excluir este aplicativo?');"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($aplicativo_para_editar): ?>
            <h2 class="mb-4">Editar Aplicativo</h2>
            <form method="POST">
                <input type="hidden" name="aplicativo_id" value="<?php echo $aplicativo_para_editar['id']; ?>">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" class="form-control" value="<?php echo htmlspecialchars($aplicativo_para_editar['nome']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição:</label>
                    <input type="text" id="descricao" name="descricao" class="form-control" value="<?php echo htmlspecialchars($aplicativo_para_editar['descricao']); ?>" required>
                </div>
                <button type="submit" name="editar" class="btn btn-primary">Salvar</button>
                <a href="listar_aplicativos.php" class="btn btn-secondary">Cancelar</a>
            </form>
        <?php endif; ?>

        <a href="dashboard.php" class="btn btn-secondary btn-voltar mt-3">Voltar ao Dashboard</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
