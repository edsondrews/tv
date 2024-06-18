<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');

// Verificar se o usuário está logado
if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Verificar se o usuário é administrador
$user_id = $_SESSION['user_id'];
$dispositivos = listarDispositivos($pdo, $user_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Listar Dispositivos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Listar Dispositivos</h1>
        <a href="dashboard.php" class="btn btn-secondary mb-3">Voltar ao Dashboard</a>
        <a href="adicionar_dispositivo.php" class="btn btn-primary mb-3">Adicionar Dispositivo</a>
        <table class="table table-striped table-custom">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dispositivos as $dispositivo): ?>
                    <tr>
                        <td><?= htmlspecialchars($dispositivo['name']) ?></td>
                        <td><?= htmlspecialchars($dispositivo['description']) ?></td>
                        <td>
                            <a href="editar_dispositivo.php?id=<?= $dispositivo['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Editar</a>
                            <a href="excluir_dispositivo.php?id=<?= $dispositivo['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Você tem certeza que deseja excluir este dispositivo?');"><i class="fas fa-trash"></i> Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
