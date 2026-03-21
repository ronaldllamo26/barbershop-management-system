<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
session_destroy();
header('Location: ' . BASE_PATH . 'views/admin/login.php');
exit;