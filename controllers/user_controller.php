<?php
session_start();
include '../config/db_config.php';
include '../models/User.php';

// Menambahkan Content Security Policy
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    // Validasi input
    if (empty($email) || empty($password)) {
        echo json_encode(["error" => "Email dan password wajib diisi."]);
        exit;
    }

    $userModel = new User($conn);
    $user = $userModel->findByEmail($email);

    if ($user && $userModel->verifyPassword($password, $user['password'])) {
        // Simpan data user ke session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['ip_address'] = $user['ip_address'];
        $_SESSION['browser'] = $user['browser'];
        $_SESSION['is_logged_in'] = true;

        echo json_encode(["success" => "Login berhasil.", "redirect" => "dashboard-ticket.php"]);
    } else {
        echo json_encode(["error" => "Email atau password salah."]);
    }
}
?>