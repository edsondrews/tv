<?php
include('config/auth.php');
include('config/db.php');

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
$stmt = $pdo->prepare("DELETE FROM servers WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);

header('Location: dashboard.php');
?>
