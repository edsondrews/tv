<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isMaster() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'master';
}

if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}
?>
