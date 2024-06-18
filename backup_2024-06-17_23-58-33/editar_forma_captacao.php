<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    $formaCaptacao = obterFormaCaptacao($pdo, $id, $user_id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['nome'];
        $custo = isset($_POST['custo']) && $_POST['custo'] !== '' ? $_POST['custo'] : null;

        atualizarFormaCaptacao($pdo, $nome, $custo, $id, $user_id);

        header('Location: listar_formas_captacao.php');
        exit;
    }
} else {
    header('Location: listar_formas_captacao.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Forma de Captação</title>
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
    <h1>Editar Forma de Captação</h1>
    <form method="post">
        <label>Nome: <input type="text" name="nome" value="<?php echo htmlspecialchars($formaCaptacao['name']); ?>" required></label>
        <label>Custo: <input type="number" name="custo" value="<?php echo htmlspecialchars($formaCaptacao['cost']); ?>"></label>
        <button type="submit">Salvar</button>
    </form>
    <a href="listar_formas_captacao.php">Voltar</a>
</body>
</html>
