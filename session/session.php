<?php
session_start();
if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
    header("Location: ../views/login.php");
    exit;
}

$userName = $_SESSION['user_name'];
?>