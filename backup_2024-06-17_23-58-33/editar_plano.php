<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');

$user_id = $_SESSION['user_id'];
if ($_SESSION['user_role'] !== 'master' && $_SESSION['user_role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$id = $_GET['id'];
$plano = obterPlano($pdo, $id, $user_id);

if (!$plano) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $period_type = $_POST['period_type'];
    $period = $_POST['period'];
    $credits_cost = $_POST['credits_cost'];
    $observation = $_POST['observation'] ?? null;

    try {
        atualizarPlano($pdo, $name, $period_type, $period, $credits_cost, $observation, $id, $user_id);
        header('Location: dashboard.php');
        exit();
    } catch (PDOException $e) {
        $message = "Erro ao atualizar plano: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Plano</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <h1>Editar Plano</h1>
    <?php if (isset($message)) echo "<p>$message</p>"; ?>
    <form method="POST">
        <label for="name">Nome:</label>
        <input type="text" id="name" name="name" value="<?php echo $plano['name']; ?>" required>

        <label for="period_type">Tipo de Período:</label>
        <input type="text" id="period_type" name="period_type" value="<?php echo $plano['period_type']; ?>" required>

        <label for="period">Período:</label>
        <input type="number" id="period" name="period" value="<?php echo $plano['period']; ?>" required>

        <label for="credits_cost">Créditos Gastos:</label>
        <input type="number" step="0.01" id="credits_cost" name="credits_cost" value="<?php echo $plano['credits_cost']; ?>" required>

        <label for="observation">Observação:</label>
        <textarea id="observation" name="observation"><?php echo $plano['observation']; ?></textarea>

        <button type="submit">Atualizar Plano</button>
    </form>
    <a href="dashboard.php">Voltar ao Dashboard</a>
    <a href="dashboard.php?logout=true">Logout</a>
</body>
</html>
