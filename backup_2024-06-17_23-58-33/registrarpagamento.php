<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $valor = $_POST['valor'];
    $data_pagamento = date('Y-m-d');  // Data de hoje
    $user_id = $_SESSION['user_id'];

    registrarPagamento($pdo, $cliente_id, $valor, $data_pagamento, $user_id);

    echo "Pagamento registrado com sucesso.";
}
?>
