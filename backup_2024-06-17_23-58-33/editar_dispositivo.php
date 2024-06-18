<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');

// Verificar se o usuário está logado
if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Obter o ID do dispositivo a ser editado
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    header('Location: listar_dispositivos.php');
    exit();
}

// Obter o dispositivo do banco de dados
$user_id = $_SESSION['user_id'];
$dispositivo = obterDispositivo($pdo, $id, $user_id);

if (!$dispositivo) {
    header('Location: listar_dispositivos.php');
    exit();
}

// Atualizar o dispositivo no banco de dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    atualizarDispositivo($pdo, $name, $description, $id, $user_id);
    header('Location: listar_dispositivos.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Dispositivo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Editar Dispositivo</h1>
        <form method="POST">
            <div class="form-group">
                <label for="name">Nome:</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($dispositivo['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Descrição:</label>
                <textarea id="description" name="description" class="form-control"><?= htmlspecialchars($dispositivo['description']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
        <a href="listar_dispositivos.php" class="btn btn-secondary btn-voltar mt-3">Voltar</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
