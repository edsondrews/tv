<?php
include('config/auth.php');
include('config/db.php');

if ($_SESSION['user_role'] !== 'master' && $_SESSION['user_role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $credit_value = $_POST['credit_value'];
    $whatsapp_session = $_POST['whatsapp_session'] ?? null;
    $panel_link = $_POST['panel_link'] ?? null;
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO servers (name, credit_value, whatsapp_session, panel_link, user_id) VALUES (?, ?, ?, ?, ?)");
    try {
        $stmt->execute([$name, $credit_value, $whatsapp_session, $panel_link, $user_id]);
        $message = "Servidor adicionado com sucesso.";
    } catch (PDOException $e) {
        $message = "Erro ao adicionar servidor: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Novo Servidor</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Adicionar Novo Servidor</h1>
        <?php if (isset($message)) echo "<div class='alert alert-info'>$message</div>"; ?>
        <form method="POST">
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
        <a href="dashboard.php" class="btn btn-secondary btn-voltar mt-3">Voltar ao Dashboard</a>
        <a href="dashboard.php?logout=true" class="btn btn-danger mt-3">Logout</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
