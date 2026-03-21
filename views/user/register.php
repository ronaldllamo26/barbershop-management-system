<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';

// Already logged in?
if (!empty($_SESSION['customer_id'])) {
    header('Location: ' . BASE_PATH . 'views/user/my_bookings.php'); exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname   = trim($_POST['first_name'] ?? '');
    $lname   = trim($_POST['last_name']  ?? '');
    $email   = trim($_POST['email']      ?? '');
    $phone   = trim($_POST['phone']      ?? '');
    $pass    = $_POST['password']        ?? '';
    $confirm = $_POST['confirm_password']?? '';

    if (!$fname || !$lname || !$email || !$phone || !$pass) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($pass) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($pass !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check if email exists
        $e = $conn->real_escape_string($email);
        $check = $conn->query("SELECT id FROM customers WHERE email='$e' LIMIT 1");
        if ($check && $check->num_rows > 0) {
            $error = 'Email already registered. Please login instead.';
        } else {
            $hash  = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 10]);
            $fn    = $conn->real_escape_string($fname);
            $ln    = $conn->real_escape_string($lname);
            $ph    = $conn->real_escape_string($phone);
            $he    = $conn->real_escape_string($hash);

            $sql = "INSERT INTO customers (first_name, last_name, email, phone, password, is_verified, is_active)
                    VALUES ('$fn','$ln','$e','$ph','$he',1,1)";
            if ($conn->query($sql)) {
                $customerId = $conn->insert_id;
                $_SESSION['customer_id']   = $customerId;
                $_SESSION['customer_name'] = $fname . ' ' . $lname;
                $_SESSION['customer_email']= $email;
                header('Location: ' . BASE_PATH . 'views/user/my_bookings.php?welcome=1'); exit;
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

$pageTitle = 'Register';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

<section class="page-hero page-hero-sm">
  <div class="page-hero-overlay"></div>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center page-hero-content">
        <span class="sec-label">Join BG</span>
        <div class="divider center"></div>
        <h1 class="page-hero-title">Create an <em style="color:var(--gold)">Account</em></h1>
        <p class="page-hero-sub">Track your bookings and manage your appointments</p>
      </div>
    </div>
  </div>
</section>

<section class="sec-pad bg-light-sec">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6">

        <div class="auth-card">
          <div class="auth-card-header">
            <h3>Create Account</h3>
            <p>Already have an account? <a href="<?= BASE_PATH ?>views/user/login.php">Sign in here</a></p>
          </div>

          <?php if ($error): ?>
          <div class="booking-alert"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="POST" novalidate>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label-dark">First Name *</label>
                <input type="text" name="first_name" class="input-dark"
                       value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>"
                       placeholder="Juan" required>
              </div>
              <div class="col-md-6">
                <label class="form-label-dark">Last Name *</label>
                <input type="text" name="last_name" class="input-dark"
                       value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>"
                       placeholder="Dela Cruz" required>
              </div>
              <div class="col-12">
                <label class="form-label-dark">Email Address *</label>
                <input type="email" name="email" class="input-dark"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="juan@email.com" required>
              </div>
              <div class="col-12">
                <label class="form-label-dark">Phone / Viber *</label>
                <input type="tel" name="phone" class="input-dark"
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                       placeholder="+63 9XX XXX XXXX" required>
              </div>
              <div class="col-md-6">
                <label class="form-label-dark">Password *</label>
                <input type="password" name="password" class="input-dark"
                       placeholder="Min 8 characters" required>
              </div>
              <div class="col-md-6">
                <label class="form-label-dark">Confirm Password *</label>
                <input type="password" name="confirm_password" class="input-dark"
                       placeholder="Repeat password" required>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-gold w-100">
                  <i class="fas fa-user-plus me-2"></i> Create Account
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