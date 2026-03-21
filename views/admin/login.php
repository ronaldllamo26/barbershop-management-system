<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');

// Already logged in?
if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE_PATH . 'views/admin/dashboard.php'); exit;
}

require_once __DIR__ . '/../../config/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $e   = $conn->real_escape_string($email);
        $res = $conn->query("SELECT * FROM admins WHERE email='$e' AND is_active=1 LIMIT 1");
        if ($res && $res->num_rows > 0) {
            $admin = $res->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id']   = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_role'] = $admin['role'];
                header('Location: ' . BASE_PATH . 'views/admin/dashboard.php'); exit;
            }
        }
        $error = 'Invalid email or password.';
    } else {
        $error = 'Please enter your email and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login | BG Barbershop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Raleway:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_PATH ?>assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-login-page">
  <div class="login-card">
    <div class="login-logo">BG</div>
    <div class="login-sub">Admin Panel</div>

    <?php if ($error): ?>
    <div class="login-error"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <label class="login-label">Email Address</label>
      <input type="email" name="email" class="login-input" placeholder="admin@bgbarbershop.com"
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>

      <label class="login-label">Password</label>
      <input type="password" name="password" class="login-input" placeholder="••••••••" required>

      <button type="submit" class="login-btn">
        <i class="fas fa-sign-in-alt me-2"></i> Sign In
      </button>
    </form>

    <p style="text-align:center;margin-top:20px;font-size:.75rem;color:var(--gray);">
      <a href="<?= BASE_PATH ?>index.php" style="color:var(--gray-l);">
        <i class="fas fa-arrow-left me-1"></i> Back to Website
      </a>
    </p>
  </div>
</div>
</body>
</html>