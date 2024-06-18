<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');

$user_id = $_SESSION['user_id'];

$id = $_GET['id'];

excluirDispositivo($pdo, $id, $user_id);
header('Location: listar_dispositivos.php');
exit();
?>
