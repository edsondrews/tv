<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');

if ($_SESSION['user_role'] !== 'master' && $_SESSION['user_role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $period_type = $_POST['period_type'];
    $period = $_POST['period'];
    $credits_cost = $_POST['credits_cost'];
    $observation = $_POST['observation'] ?? null;
    $user_id = $_SESSION['user_id'];

    try {
        adicionarPlano($pdo, $name, $period_type, $period, $credits_cost, $observation, $user_id);
        $message = "Plano adicionado com sucesso.";
    } catch (PDOException $e) {
        $message = "Erro ao adicionar plano: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Novo Plano</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Adicionar Novo Plano</h1>
        <?php if (isset($message)) echo "<div class='alert alert-info'>$message</div>"; ?>
        <form method="POST">
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
        <a href="dashboard.php" class="btn btn-secondary btn-voltar mt-3">Voltar ao Dashboard</a>
        <a href="dashboard.php?logout=true" class="btn btn-danger mt-3">Logout</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
