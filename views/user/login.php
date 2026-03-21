<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';

if (!empty($_SESSION['customer_id'])) {
    header('Location: ' . BASE_PATH . 'views/user/my_bookings.php'); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if (!$email || !$pass) {
        $error = 'Please enter your email and password.';
    } else {
        $e   = $conn->real_escape_string($email);
        $res = $conn->query("SELECT * FROM customers WHERE email='$e' AND is_active=1 LIMIT 1");
        if ($res && $res->num_rows > 0) {
            $customer = $res->fetch_assoc();
            if (password_verify($pass, $customer['password'])) {
                $_SESSION['customer_id']    = $customer['id'];
                $_SESSION['customer_name']  = $customer['first_name'] . ' ' . $customer['last_name'];
                $_SESSION['customer_email'] = $customer['email'];
                $redirect = $_GET['redirect'] ?? BASE_PATH . 'views/user/my_bookings.php';
                header('Location: ' . $redirect); exit;
            }
        }
        $error = 'Invalid email or password.';
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

          <?php if (!empty($_GET['registered'])): ?>
          <div style="background:#f0fdf4;border-left:4px solid #22c55e;color:#15803d;padding:12px 16px;font-size:.85rem;margin-bottom:16px;">
            <i class="fas fa-check-circle me-2"></i>Account created! Please sign in.
          </div>
          <?php endif; ?>

          <form method="POST" novalidate>
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label-dark">Email Address</label>
                <input type="email" name="email" class="input-dark"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="juan@email.com" required autofocus>
              </div>
              <div class="col-12">
                <label class="form-label-dark">Password</label>
                <input type="password" name="password" class="input-dark"
                       placeholder="••••••••" required>
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

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>