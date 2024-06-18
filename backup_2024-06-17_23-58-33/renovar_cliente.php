<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $periodo = $_POST['periodo'];

    if (renovarCliente($pdo, $cliente_id, $_SESSION['user_id'], $periodo)) {
        header('Location: listar_clientes.php?renovado=1');
    } else {
        header('Location: listar_clientes.php?erro=1');
    }
    exit;
}
?>
