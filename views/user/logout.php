<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Only unset customer session vars — preserve admin session
unset(
    $_SESSION['customer_id'],
    $_SESSION['customer_name'],
    $_SESSION['customer_email']
);

// Redirect to homepage
header('Location: /bg-barbershop/index.php');
exit;