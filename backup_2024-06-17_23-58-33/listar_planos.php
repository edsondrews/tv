<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');

// Verificar se o usuário está logado
if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Função para listar planos
$planos = listarPlanos($pdo, $_SESSION['user_id']);

// Processar ações de formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['excluir'])) {
        $plano_id = $_POST['plano_id'];
        excluirPlano($pdo, $plano_id);
        header('Location: listar_planos.php');
        exit();
    } elseif (isset($_POST['editar'])) {
        $plano_id = $_POST['plano_id'];
        $name = $_POST['name'];
        $period_type = $_POST['period_type'];
        $period = $_POST['period'];
        $credits_cost = $_POST['credits_cost'];
        $observation = $_POST['observation'] ?? null;
        atualizarPlano($pdo, $name, $period_type, $period, $credits_cost, $observation, $plano_id, $_SESSION['user_id']);
        header('Location: listar_planos.php');
        exit();
    } elseif (isset($_POST['adicionar'])) {
        $name = $_POST['name'];
        $period_type = $_POST['period_type'];
        $period = $_POST['period'];
        $credits_cost = $_POST['credits_cost'];
        $observation = $_POST['observation'] ?? null;
        adicionarPlano($pdo, $name, $period_type, $period, $credits_cost, $observation, $_SESSION['user_id']);
        header('Location: listar_planos.php');
        exit();
    }
}

$plano_para_editar = null;
if (isset($_GET['editar_id'])) {
    $plano_para_editar = obterPlano($pdo, $_GET['editar_id'], $_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Listar Planos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Planos</h1>
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalAdicionar">Adicionar Novo Plano</button>
        <table class="table table-striped table-custom">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tipo de Período</th>
                    <th>Período</th>
                    <th>Créditos Gastos</th>
                    <th>Observação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($planos as $plano): ?>
                    <tr>
                        <td><?= htmlspecialchars($plano['name']) ?></td>
                        <td><?= htmlspecialchars($plano['period_type']) ?></td>
                        <td><?= htmlspecialchars($plano['period']) ?></td>
                        <td><?= htmlspecialchars($plano['credits_cost']) ?></td>
                        <td><?= htmlspecialchars($plano['observation']) ?></td>
                        <td>
                            <a href="listar_planos.php?editar_id=<?= $plano['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Editar</a>
                            <form method="post" style="display:inline;" class="form-excluir-plano">
                                <input type="hidden" name="plano_id" value="<?= $plano['id'] ?>">
                                <button type="submit" name="excluir" class="btn btn-sm btn-danger" onclick="return confirm('Você tem certeza que deseja excluir este plano?');"><i class="fas fa-trash"></i> Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($plano_para_editar): ?>
            <h2 class="mb-4">Editar Plano</h2>
            <form method="POST">
                <input type="hidden" name="plano_id" value="<?= $plano_para_editar['id'] ?>">
                <div class="form-group">
                    <label for="name">Nome:</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($plano_para_editar['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="period_type">Tipo de Período:</label>
                    <input type="text" id="period_type" name="period_type" class="form-control" value="<?= htmlspecialchars($plano_para_editar['period_type']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="period">Período:</label>
                    <input type="number" id="period" name="period" class="form-control" value="<?= htmlspecialchars($plano_para_editar['period']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="credits_cost">Créditos Gastos:</label>
                    <input type="number" step="0.01" id="credits_cost" name="credits_cost" class="form-control" value="<?= htmlspecialchars($plano_para_editar['credits_cost']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="observation">Observação:</label>
                    <textarea id="observation" name="observation" class="form-control"><?= htmlspecialchars($plano_para_editar['observation']) ?></textarea>
                </div>
                <button type="submit" name="editar" class="btn btn-primary">Salvar</button>
                <a href="listar_planos.php" class="btn btn-secondary btn-voltar mt-3">Cancelar</a>
            </form>
        <?php endif; ?>

        <!-- Modal para adicionar plano -->
        <div class="modal fade" id="modalAdicionar" tabindex="-1" role="dialog" aria-labelledby="modalAdicionarLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAdicionarLabel">Adicionar Novo Plano</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="adicionar" value="1">
                            <div class="form-group">
                                <label for="name">Nome:</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="period_type">Tipo de Período:</label>
                                <input type="text" id="period_type" name="period_type" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="period">Período:</label>
                                <input type="number" id="period" name="period" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="credits_cost">Créditos Gastos:</label>
                                <input type="number" step="0.01" id="credits_cost" name="credits_cost" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="observation">Observação:</label>
                                <textarea id="observation" name="observation" class="form-control"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <a href="dashboard.php" class="btn btn-secondary btn-voltar mt-3">Voltar ao Dashboard</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
