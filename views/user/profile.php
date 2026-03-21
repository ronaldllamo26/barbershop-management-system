<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';

// Require login
if (empty($_SESSION['customer_id'])) {
    header('Location: ' . BASE_PATH . 'views/user/login.php'); exit;
}

$customerId = intval($_SESSION['customer_id']);
$alert = '';

// Load customer data
$res      = $conn->query("SELECT * FROM customers WHERE id=$customerId LIMIT 1");
$customer = $res->fetch_assoc();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── Update Profile ──
    if ($action === 'update_profile') {
        $fname = $conn->real_escape_string(trim($_POST['first_name']));
        $lname = $conn->real_escape_string(trim($_POST['last_name']));
        $phone = $conn->real_escape_string(trim($_POST['phone']));
        $email = $conn->real_escape_string(trim($_POST['email']));

        if (!$fname || !$lname || !$phone || !$email) {
            $alert = 'error|Please fill in all required fields.';
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $alert = 'error|Please enter a valid email address.';
        } else {
            // Check if email taken by someone else
            $check = $conn->query("SELECT id FROM customers WHERE email='$email' AND id!=$customerId LIMIT 1");
            if ($check && $check->num_rows > 0) {
                $alert = 'error|Email already used by another account.';
            } else {
                $conn->query("UPDATE customers SET first_name='$fname', last_name='$lname',
                              phone='$phone', email='$email' WHERE id=$customerId");
                // Update session
                $_SESSION['customer_name']  = $_POST['first_name'] . ' ' . $_POST['last_name'];
                $_SESSION['customer_email'] = $_POST['email'];
                // Refresh customer data
                $res      = $conn->query("SELECT * FROM customers WHERE id=$customerId LIMIT 1");
                $customer = $res->fetch_assoc();
                $alert = 'success|Profile updated successfully!';
            }
        }
    }

    // ── Change Password ──
    if ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!password_verify($current, $customer['password'])) {
            $alert = 'error|Current password is incorrect.';
        } elseif (strlen($new) < 8) {
            $alert = 'error|New password must be at least 8 characters.';
        } elseif ($new !== $confirm) {
            $alert = 'error|New passwords do not match.';
        } else {
            $hash = $conn->real_escape_string(password_hash($new, PASSWORD_BCRYPT, ['cost'=>10]));
            $conn->query("UPDATE customers SET password='$hash' WHERE id=$customerId");
            $alert = 'success|Password changed successfully!';
        }
    }
}

// Load booking stats
$stats = $conn->query(
    "SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN status='pending' OR status='confirmed' THEN 1 ELSE 0 END) AS upcoming,
        COALESCE(SUM(CASE WHEN status='completed' THEN aps.price_snapshot ELSE 0 END),0) AS spent
     FROM appointments a
     LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
     WHERE a.customer_id = $customerId"
)->fetch_assoc();

$pageTitle = 'My Profile';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

<section class="page-hero page-hero-sm">
  <div class="page-hero-overlay"></div>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center page-hero-content">
        <span class="sec-label">My Account</span>
        <div class="divider center"></div>
        <h1 class="page-hero-title">My <em style="color:var(--gold)">Profile</em></h1>
        <p class="page-hero-sub">Manage your account information</p>
      </div>
    </div>
  </div>
</section>

<section class="sec-pad bg-light-sec">
  <div class="container">

    <?php if ($alert): [$type, $msg] = explode('|', $alert, 2); ?>
    <div style="background:<?= $type==='success' ? '#f0fdf4' : '#fef2f2' ?>;
                border-left:4px solid <?= $type==='success' ? '#22c55e' : '#ef4444' ?>;
                color:<?= $type==='success' ? '#15803d' : '#b91c1c' ?>;
                padding:14px 18px;font-size:.88rem;margin-bottom:24px;">
      <i class="fas fa-<?= $type==='success' ? 'check' : 'exclamation' ?>-circle me-2"></i>
      <?= htmlspecialchars($msg) ?>
    </div>
    <?php endif; ?>

    <!-- Account Header -->
    <div class="my-account-header mb-4">
      <div class="mah-info">
        <div class="mah-avatar" style="font-size:1.4rem;">
          <?= strtoupper(substr($customer['first_name'], 0, 1)) ?>
        </div>
        <div>
          <h4><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></h4>
          <span><?= htmlspecialchars($customer['email']) ?></span>
        </div>
      </div>
      <div class="d-flex gap-2 flex-wrap">
        <a href="/bg-barbershop/views/user/my_bookings.php" class="btn-outline-dark">
          <i class="fas fa-calendar-check me-2"></i>My Bookings
        </a>
        <a href="/bg-barbershop/views/user/logout.php" class="btn-black">
          <i class="fas fa-sign-out-alt me-2"></i>Logout
        </a>
      </div>
    </div>

    <!-- Stats Strip -->
    <div class="profile-stats mb-4">
      <div class="ps-stat">
        <div class="ps-num"><?= $stats['total'] ?></div>
        <div class="ps-lbl">Total Bookings</div>
      </div>
      <div class="ps-stat">
        <div class="ps-num" style="color:#15803d"><?= $stats['completed'] ?></div>
        <div class="ps-lbl">Completed</div>
      </div>
      <div class="ps-stat">
        <div class="ps-num" style="color:var(--gold-d)"><?= $stats['upcoming'] ?></div>
        <div class="ps-lbl">Upcoming</div>
      </div>
      <div class="ps-stat">
        <div class="ps-num">₱<?= number_format($stats['spent']) ?></div>
        <div class="ps-lbl">Total Spent</div>
      </div>
    </div>

    <div class="row g-4">

      <!-- Update Profile -->
      <div class="col-lg-6">
        <div class="auth-card">
          <div class="auth-card-header">
            <h3>Personal Information</h3>
            <p>Update your name, email, and phone number</p>
          </div>
          <form method="POST" novalidate>
            <input type="hidden" name="action" value="update_profile">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label-dark">First Name *</label>
                <input type="text" name="first_name" class="input-dark"
                       value="<?= htmlspecialchars($customer['first_name']) ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label-dark">Last Name *</label>
                <input type="text" name="last_name" class="input-dark"
                       value="<?= htmlspecialchars($customer['last_name']) ?>" required>
              </div>
              <div class="col-12">
                <label class="form-label-dark">Email Address *</label>
                <input type="email" name="email" class="input-dark"
                       value="<?= htmlspecialchars($customer['email']) ?>" required>
              </div>
              <div class="col-12">
                <label class="form-label-dark">Phone / Viber *</label>
                <input type="tel" name="phone" class="input-dark"
                       value="<?= htmlspecialchars($customer['phone']) ?>" required>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-gold w-100">
                  <i class="fas fa-save me-2"></i>Save Changes
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Change Password -->
      <div class="col-lg-6">
        <div class="auth-card">
          <div class="auth-card-header">
            <h3>Change Password</h3>
            <p>Keep your account secure with a strong password</p>
          </div>
          <form method="POST" novalidate>
            <input type="hidden" name="action" value="change_password">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label-dark">Current Password *</label>
                <input type="password" name="current_password" class="input-dark"
                       placeholder="••••••••" required>
              </div>
              <div class="col-12">
                <label class="form-label-dark">New Password *</label>
                <input type="password" name="new_password" class="input-dark"
                       placeholder="Min 8 characters" required>
              </div>
              <div class="col-12">
                <label class="form-label-dark">Confirm New Password *</label>
                <input type="password" name="confirm_password" class="input-dark"
                       placeholder="Repeat new password" required>
              </div>
              <!-- Password strength indicator -->
              <div class="col-12">
                <div class="pwd-strength-wrap" style="display:none;">
                  <div class="pwd-strength-bar">
                    <div class="pwd-strength-fill" id="pwdFill"></div>
                  </div>
                  <span class="pwd-strength-label" id="pwdLabel"></span>
                </div>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-black w-100">
                  <i class="fas fa-key me-2"></i>Change Password
                </button>
              </div>
            </div>
          </form>
        </div>

        <!-- Danger Zone -->
        <div class="auth-card mt-4" style="border-color:rgba(239,68,68,.2);">
          <div class="auth-card-header" style="border-color:rgba(239,68,68,.2);">
            <h3 style="color:#b91c1c;">Account Actions</h3>
            <p>Manage your account</p>
          </div>
          <div style="padding-top:4px;">
            <a href="/bg-barbershop/views/user/my_bookings.php" class="btn-outline-dark w-100 mb-2"
               style="justify-content:center;">
              <i class="fas fa-history me-2"></i>View Booking History
            </a>
            <a href="/bg-barbershop/views/user/booking.php" class="btn-gold w-100"
               style="justify-content:center;">
              <i class="fas fa-calendar-plus me-2"></i>Book New Appointment
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<script>
// Password strength checker
document.querySelector('[name="new_password"]').addEventListener('input', function() {
  const val  = this.value;
  const wrap = document.querySelector('.pwd-strength-wrap');
  const fill = document.getElementById('pwdFill');
  const lbl  = document.getElementById('pwdLabel');

  if (!val) { wrap.style.display = 'none'; return; }
  wrap.style.display = 'flex';

  let strength = 0;
  if (val.length >= 8)              strength++;
  if (/[A-Z]/.test(val))           strength++;
  if (/[0-9]/.test(val))           strength++;
  if (/[^A-Za-z0-9]/.test(val))    strength++;

  const levels = [
    { label: 'Weak',   color: '#ef4444', width: '25%' },
    { label: 'Fair',   color: '#f59e0b', width: '50%' },
    { label: 'Good',   color: '#3b82f6', width: '75%' },
    { label: 'Strong', color: '#22c55e', width: '100%' },
  ];
  const lvl     = levels[Math.min(strength - 1, 3)] || levels[0];
  fill.style.width      = lvl.width;
  fill.style.background = lvl.color;
  lbl.textContent       = lvl.label;
  lbl.style.color       = lvl.color;
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>