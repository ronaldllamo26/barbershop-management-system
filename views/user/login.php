<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/security.php';

security_headers();
secure_session_start();

if (!empty($_SESSION['customer_id'])) {
    header('Location: ' . BASE_PATH . 'views/user/my_bookings.php'); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    honeypot_check();
    csrf_verify();

    // reCAPTCHA verify
    $token = $_POST['recaptcha_token'] ?? '';
    if (!recaptcha_verify($token)) { $error = 'Security check failed. Please try again.'; }

    $email = clean_email($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    // Check lockout
    $lockout = check_lockout('customer_login_' . $email);
    if ($lockout['locked']) {
        $error = $lockout['message'];
    } elseif (!$email || !$pass) {
        $error = 'Please enter your email and password.';
    } else {
        // Rate limit — max 10 per hour
        $rate = rate_limit_ip('customer_login', 10, 3600);
        if ($rate['blocked']) {
            $error = $rate['message'];
        } else {
            $e   = $conn->real_escape_string($email);
            $res = $conn->query("SELECT * FROM customers WHERE email='$e' AND is_active=1 LIMIT 1");

            if ($res && $res->num_rows > 0) {
                $customer = $res->fetch_assoc();
                if (password_verify($pass, $customer['password'])) {
                    clear_failed_attempts('customer_login_' . $email);
                    session_regenerate_id(true);
                    $_SESSION['customer_id']    = $customer['id'];
                    $_SESSION['customer_name']  = $customer['first_name'] . ' ' . $customer['last_name'];
                    $_SESSION['customer_email'] = $customer['email'];
                    $redirect = $_GET['redirect'] ?? BASE_PATH . 'views/user/my_bookings.php';
                    header('Location: ' . $redirect); exit;
                }
            }

            record_failed_attempt('customer_login_' . $email, 5, 15);
            $lockout  = check_lockout('customer_login_' . $email);
            $attempts = $_SESSION['lockout_' . md5('customer_login_' . $email) . '_attempts'] ?? 0;
            $left     = 5 - $attempts;

            if ($lockout['locked']) {
                $error = $lockout['message'];
            } else {
                $error = 'Invalid email or password.' . ($left <= 3 ? " {$left} attempt(s) left." : '');
            }
        }
    }
}

$pageTitle = 'Login';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

<section class="page-hero page-hero-sm">
  <div class="page-hero-overlay"></div>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center page-hero-content">
        <span class="sec-label">Welcome Back</span>
        <div class="divider center"></div>
        <h1 class="page-hero-title">Sign <em style="color:var(--gold)">In</em></h1>
        <p class="page-hero-sub">Access your bookings and appointment history</p>
      </div>
    </div>
  </div>
</section>

<section class="sec-pad bg-light-sec">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-5">
        <div class="auth-card">
          <div class="auth-card-header">
            <h3>Sign In</h3>
            <p>No account yet? <a href="<?= BASE_PATH ?>views/user/register.php">Register here</a></p>
          </div>

          <?php if ($error): ?>
          <div class="booking-alert"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="POST" novalidate>
            <?= csrf_field() ?>
            <?= honeypot_field() ?>
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label-dark">Email Address</label>
                <input type="email" name="email" class="input-dark"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="juan@email.com" required autofocus>
              </div>
              <div class="col-12">
  <label class="form-label-dark">Password</label>
  <div style="position:relative;">
    <input type="text" name="password" class="input-dark"
           placeholder="••••••••" id="userPwdField" required
           autocomplete="off"
           style="padding-right:44px;-webkit-text-security:disc;">
    <button type="button" onclick="toggleUserPwd()"
            style="position:absolute;right:14px;top:50%;transform:translateY(-50%);
                   background:none;border:none;color:var(--gray-d);cursor:pointer;outline:none;">
      <i class="fas fa-eye-slash" id="userPwdIcon"></i>
    </button>
  </div>
</div>
              <div class="col-12">
                <button type="submit" class="btn-gold w-100">
                  <i class="fas fa-sign-in-alt me-2"></i> Sign In
                </button>
              </div>
              <div class="col-12 text-center">
                <p style="font-size:.8rem;color:var(--gray-d);">
                  Or <a href="<?= BASE_PATH ?>views/user/booking.php">book as guest</a> — no account needed.
                </p>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
let pwdVisible = false;
function toggleUserPwd() {
  const f = document.getElementById('userPwdField');
  const i = document.getElementById('userPwdIcon');
  pwdVisible = !pwdVisible;
  if (pwdVisible) {
    f.style.webkitTextSecurity = 'none';
    i.classList.remove('fa-eye-slash');
    i.classList.add('fa-eye');
  } else {
    f.style.webkitTextSecurity = 'disc';
    i.classList.remove('fa-eye');
    i.classList.add('fa-eye-slash');
  }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>