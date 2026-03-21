<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function requireAdmin() {
    if (empty($_SESSION['admin_id'])) {
        header('Location: ' . BASE_PATH . 'views/admin/login.php');
        exit;
    }
}

function adminLogout() {
    session_destroy();
    header('Location: ' . BASE_PATH . 'views/admin/login.php');
    exit;
}

function currentAdmin() {
    return [
        'id'   => $_SESSION['admin_id']   ?? 0,
        'name' => $_SESSION['admin_name'] ?? 'Admin',
        'role' => $_SESSION['admin_role'] ?? 'admin',
    ];
}