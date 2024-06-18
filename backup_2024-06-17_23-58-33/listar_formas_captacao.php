<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');

// Verificar se o usuário está logado
if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Função para listar formas de pagamento
$formas_pagamento = listarFormasPagamento($pdo, $_SESSION['user_id']);

// Processar ações de formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['excluir'])) {
        $forma_pagamento_id = $_POST['forma_pagamento_id'];
        excluirFormaPagamento($pdo, $forma_pagamento_id);
        header('Location: listar_formas_pagamento.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Listar Formas de Pagamento</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Formas de Pagamento</h1>
        <a href="adicionar_forma_pagamento.php" class="btn btn-primary mb-3">Adicionar Nova Forma de Pagamento</a>
        <table class="table table-striped table-custom">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($formas_pagamento as $forma_pagamento): ?>
                    <tr>
                        <td><?= htmlspecialchars($forma_pagamento['nome']) ?></td>
                        <td>
                            <a href="editar_forma_pagamento.php?id=<?= $forma_pagamento['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Editar</a>
                            <form method="post" style="display:inline;" class="form-excluir-forma-pagamento">
                                <input type="hidden" name="forma_pagamento_id" value="<?= $forma_pagamento['id'] ?>">
                                <button type="submit" name="excluir" class="btn btn-sm btn-danger" onclick="return confirm('Você tem certeza que deseja excluir esta forma de pagamento?');"><i class="fas fa-trash"></i> Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary btn-voltar mt-3">Voltar ao Dashboard</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
