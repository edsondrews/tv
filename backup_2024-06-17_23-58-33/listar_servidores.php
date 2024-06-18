<?php
include('config/auth.php');
include('config/db.php');

// Verificar se o usuário está logado e tem permissão
if ($_SESSION['user_role'] !== 'master' && $_SESSION['user_role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Função para listar servidores
$stmt = $pdo->prepare("SELECT * FROM servers WHERE user_id = ?");
$stmt->execute([$user_id]);
$servidores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Processar ações de formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['excluir'])) {
        $servidor_id = $_POST['servidor_id'];
        $stmt = $pdo->prepare("DELETE FROM servers WHERE id = ? AND user_id = ?");
        $stmt->execute([$servidor_id, $user_id]);
        header('Location: listar_servidores.php');
        exit();
    } elseif (isset($_POST['editar'])) {
        $servidor_id = $_POST['servidor_id'];
        $name = $_POST['name'];
        $credit_value = $_POST['credit_value'];
        $whatsapp_session = $_POST['whatsapp_session'] ?? null;
        $panel_link = $_POST['panel_link'] ?? null;
        $stmt = $pdo->prepare("UPDATE servers SET name = ?, credit_value = ?, whatsapp_session = ?, panel_link = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$name, $credit_value, $whatsapp_session, $panel_link, $servidor_id, $user_id]);
        header('Location: listar_servidores.php');
        exit();
    } elseif (isset($_POST['adicionar'])) {
        $name = $_POST['name'];
        $credit_value = $_POST['credit_value'];
        $whatsapp_session = $_POST['whatsapp_session'] ?? null;
        $panel_link = $_POST['panel_link'] ?? null;
        $stmt = $pdo->prepare("INSERT INTO servers (name, credit_value, whatsapp_session, panel_link, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $credit_value, $whatsapp_session, $panel_link, $user_id]);
        header('Location: listar_servidores.php');
        exit();
    }
}

$servidor_para_editar = null;
if (isset($_GET['editar_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM servers WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['editar_id'], $user_id]);
    $servidor_para_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Listar Servidores</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Servidores</h1>
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalAdicionar">Adicionar Novo Servidor</button>
        <table class="table table-striped table-custom">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Valor Crédito</th>
                    <th>Sessão WhatsApp</th>
                    <th>Link Painel</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servidores as $servidor): ?>
                    <tr>
                        <td><?= htmlspecialchars($servidor['name']) ?></td>
                        <td><?= htmlspecialchars($servidor['credit_value']) ?></td>
                        <td><?= htmlspecialchars($servidor['whatsapp_session']) ?></td>
                        <td><?= htmlspecialchars($servidor['panel_link']) ?></td>
                        <td>
                            <a href="listar_servidores.php?editar_id=<?= $servidor['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Editar</a>
                            <form method="post" style="display:inline;" class="form-excluir-servidor">
                                <input type="hidden" name="servidor_id" value="<?= $servidor['id'] ?>">
                                <button type="submit" name="excluir" class="btn btn-sm btn-danger" onclick="return confirm('Você tem certeza que deseja excluir este servidor?');"><i class="fas fa-trash"></i> Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($servidor_para_editar): ?>
            <h2 class="mb-4">Editar Servidor</h2>
            <form method="POST">
                <input type="hidden" name="servidor_id" value="<?= $servidor_para_editar['id'] ?>">
                <div class="form-group">
                    <label for="name">Nome:</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($servidor_para_editar['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="credit_value">Valor Crédito:</label>
                    <input type="number" step="0.01" id="credit_value" name="credit_value" class="form-control" value="<?= htmlspecialchars($servidor_para_editar['credit_value']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="whatsapp_session">Sessão WhatsApp:</label>
                    <input type="text" id="whatsapp_session" name="whatsapp_session" class="form-control" value="<?= htmlspecialchars($servidor_para_editar['whatsapp_session']) ?>">
                </div>
                <div class="form-group">
                    <label for="panel_link">Link Painel:</label>
                    <input type="text" id="panel_link" name="panel_link" class="form-control" value="<?= htmlspecialchars($servidor_para_editar['panel_link']) ?>">
                </div>
                <button type="submit" name="editar" class="btn btn-primary">Salvar</button>
                <a href="listar_servidores.php" class="btn btn-secondary btn-voltar mt-3">Cancelar</a>
            </form>
        <?php endif; ?>

        <!-- Modal para adicionar servidor -->
        <div class="modal fade" id="modalAdicionar" tabindex="-1" role="dialog" aria-labelledby="modalAdicionarLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAdicionarLabel">Adicionar Novo Servidor</h5>
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
                                <label for="credit_value">Valor Crédito:</label>
                                <input type="number" step="0.01" id="credit_value" name="credit_value" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="whatsapp_session">Sessão WhatsApp:</label>
                                <input type="text" id="whatsapp_session" name="whatsapp_session" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="panel_link">Link Painel:</label>
                                <input type="text" id="panel_link" name="panel_link" class="form-control">
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
