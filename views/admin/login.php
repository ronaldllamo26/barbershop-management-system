<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/security.php';

security_headers();
secure_session_start();

// Already logged in?
if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE_PATH . 'views/admin/dashboard.php'); exit;
}

// Session timeout message
$timeout = !empty($_GET['timeout']);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Honeypot check
    honeypot_check();

    // reCAPTCHA verify
    $token = $_POST['recaptcha_token'] ?? '';
    if (!empty($token) && !recaptcha_verify($token)) { $error = 'Security check failed. Please try again.'; }

    $email    = clean_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Check lockout
    $lockout = check_lockout('admin_login_' . $email);
    if ($lockout['locked']) {
        $error = $lockout['message'];
    } elseif (!$email || !$password) {
        $error = 'Please enter your email and password.';
    } else {
        // Rate limit — max 10 attempts per hour per IP
        $rate = rate_limit_ip('admin_login', 10, 3600);
        if ($rate['blocked']) {
            $error = $rate['message'];
        } else {
            $e   = $conn->real_escape_string($email);
            $res = $conn->query("SELECT * FROM admins WHERE email='$e' AND is_active=1 LIMIT 1");

            if ($res && $res->num_rows > 0) {
                $admin = $res->fetch_assoc();
                if (password_verify($password, $admin['password'])) {
                    // Clear failed attempts
                    clear_failed_attempts('admin_login_' . $email);

                    // Regenerate session on login
                    session_regenerate_id(true);

                    $_SESSION['admin_id']            = $admin['id'];
                    $_SESSION['admin_name']          = $admin['name'];
                    $_SESSION['admin_role']          = $admin['role'];
                    $_SESSION['admin_last_activity'] = time();
                    $_SESSION['last_regenerated']    = time();

                    header('Location: ' . BASE_PATH . 'views/admin/dashboard.php'); exit;
                }
            }

            // Failed attempt
            record_failed_attempt('admin_login_' . $email, 5, 15);
            $lockout  = check_lockout('admin_login_' . $email);
            $attempts = $_SESSION['lockout_' . md5('admin_login_' . $email) . '_attempts'] ?? 0;
            $left     = 5 - $attempts;

            if ($lockout['locked']) {
                $error = $lockout['message'];
            } else {
                $error = 'Invalid email or password.' . ($left <= 3 ? " {$left} attempt(s) remaining before lockout." : '');
            }
        }
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
  <link rel="stylesheet" href="/bg-barbershop/assets/css/admin.css">
  <?= recaptcha_script() ?>
</head>
<body class="admin-body">
<div class="admin-login-page">
  <div class="login-card">
    <div class="login-logo">BG</div>
    <div class="login-sub">Admin Panel</div>

    <?php if ($timeout): ?>
    <div class="login-error" style="background:rgba(234,179,8,.1);border-color:rgba(234,179,8,.3);color:#fcd34d;">
      <i class="fas fa-clock me-2"></i>Session expired. Please sign in again.
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="login-error"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <?= csrf_field() ?>
      <?= honeypot_field() ?>

      <label class="login-label">Email Address</label>
      <input type="email" name="email" class="login-input"
             placeholder="admin@bgbarbershop.com"
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
             required autofocus>

      <label class="login-label">Password</label>
      <div style="position:relative;">
        <input type="password" name="password" class="login-input"
               placeholder="••••••••" id="pwdField" required
               autocomplete="current-password">
        <button type="button" onclick="togglePwd()"
                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--gray-l);cursor:pointer;outline:none;">
          <i class="fas fa-eye-slash" id="pwdIcon"></i>
        </button>
      </div>

      <?= recaptcha_field('admin_login') ?>
      <button type="submit" class="login-btn">
        <i class="fas fa-sign-in-alt me-2"></i> Sign In
      </button>
    </form>

    <p style="text-align:center;margin-top:20px;font-size:.75rem;color:var(--gray);">
      <a href="/bg-barbershop/index.php" style="color:var(--gray-l);">
        <i class="fas fa-arrow-left me-1"></i> Back to Website
      </a>
    </p>
  </div>
</div>
<script>
function togglePwd() {
  const f = document.getElementById('pwdField');
  const i = document.getElementById('pwdIcon');
  if (f.type === 'password') {
    // Show password — open eye
    f.type = 'text';
    i.classList.remove('fa-eye-slash');
    i.classList.add('fa-eye');
  } else {
    // Hide password — closed eye
    f.type = 'password';
    i.classList.remove('fa-eye');
    i.classList.add('fa-eye-slash');
  }
}
</script>
</body>
</html>