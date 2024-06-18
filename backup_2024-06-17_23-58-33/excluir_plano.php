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
try {
    excluirPlano($pdo, $id, $user_id);
    header('Location: dashboard.php');
} catch (PDOException $e) {
    die("Erro ao excluir plano: " . $e->getMessage());
}
?>
