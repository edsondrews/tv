<?php
include('config/functions.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}
?>
