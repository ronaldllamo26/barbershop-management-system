<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';

// Require login
if (empty($_SESSION['customer_id'])) {
    header('Location: ' . BASE_PATH . 'views/user/login.php'); exit;
}

$customerId = intval($_SESSION['customer_id']);

// Cancel booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'cancel') {
    $apptId = intval($_POST['appt_id']);

    // Get appointment before cancelling (need email details)
    $apptRes = $conn->query(
        "SELECT a.*, CONCAT(b.first_name,' ',b.last_name) AS barber_name
         FROM appointments a
         LEFT JOIN barbers b ON b.id = a.barber_id
         WHERE a.id=$apptId AND a.customer_id=$customerId
           AND a.status IN ('pending','confirmed') LIMIT 1"
    );

    if ($apptRes && $apptRes->num_rows > 0) {
        $appt = $apptRes->fetch_assoc();
        $conn->query("UPDATE appointments SET status='cancelled' WHERE id=$apptId");

        // Send cancellation email
        try {
            require_once __DIR__ . '/../../config/mailer.php';
            $emailTo   = !empty($appt['guest_email']) ? $appt['guest_email'] : $customer['email'] ?? '';
            $emailName = $appt['guest_name'];
            if ($emailTo) {
                sendCancellationNotice($emailTo, $emailName, $appt);
            }
        } catch (Throwable $e) {
            error_log('Cancellation email error: ' . $e->getMessage());
        }
    }
}

// Get bookings
$bookings = $conn->query(
    "SELECT a.*, b.first_name AS bfname, b.last_name AS blname,
            GROUP_CONCAT(s.name SEPARATOR ', ') AS services,
            SUM(aps.price_snapshot) AS total_price
     FROM appointments a
     LEFT JOIN barbers b ON b.id = a.barber_id
     LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
     LEFT JOIN services s ON s.id = aps.service_id
     WHERE a.customer_id = $customerId
     GROUP BY a.id
     ORDER BY a.appointment_date DESC, a.start_time DESC"
);

$pageTitle = 'My Bookings';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

$welcome = !empty($_GET['welcome']);
?>

<section class="page-hero page-hero-sm">
  <div class="page-hero-overlay"></div>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center page-hero-content">
        <span class="sec-label">My Account</span>
        <div class="divider center"></div>
        <h1 class="page-hero-title">My <em style="color:var(--gold)">Bookings</em></h1>
        <p class="page-hero-sub">Welcome back, <?= htmlspecialchars($_SESSION['customer_name']) ?>!</p>
      </div>
    </div>
  </div>
</section>

<section class="sec-pad bg-light-sec">
  <div class="container">

    <?php if ($welcome): ?>
    <div style="background:#f0fdf4;border-left:4px solid #22c55e;color:#15803d;padding:16px 20px;font-size:.88rem;margin-bottom:24px;">
      <i class="fas fa-check-circle me-2"></i>
      <strong>Welcome to BG Barbershop!</strong> Your account has been created successfully.
    </div>
    <?php endif; ?>

    <!-- Account Header -->
    <div class="my-account-header mb-4">
      <div class="mah-info">
        <div class="mah-avatar"><i class="fas fa-user"></i></div>
        <div>
          <h4><?= htmlspecialchars($_SESSION['customer_name']) ?></h4>
          <span><?= htmlspecialchars($_SESSION['customer_email']) ?></span>
        </div>
      </div>
      <div class="d-flex gap-2 flex-wrap">
        <a href="/bg-barbershop/views/user/booking.php" class="btn-gold">
          <i class="fas fa-calendar-plus me-2"></i>Book Again
        </a>
        <a href="/bg-barbershop/views/user/profile.php" class="btn-outline-dark">
          <i class="fas fa-user-edit me-2"></i>Edit Profile
        </a>
        <a href="/bg-barbershop/views/user/logout.php" class="btn-black">
          <i class="fas fa-sign-out-alt me-2"></i>Logout
        </a>
      </div>
    </div>

    <!-- Bookings List -->
    <?php if (!$bookings || $bookings->num_rows === 0): ?>
    <div class="text-center py-5">
      <i class="fas fa-calendar-times" style="font-size:3rem;color:var(--gray-l);margin-bottom:16px;display:block;"></i>
      <h4 style="font-family:var(--font-h);color:var(--black);margin-bottom:8px;">No bookings yet</h4>
      <p style="color:var(--gray-d);margin-bottom:24px;">Book your first appointment at BG Barbershop!</p>
      <a href="<?= BASE_PATH ?>views/user/booking.php" class="btn-gold">
        <i class="fas fa-calendar-check me-2"></i>Book Now
      </a>
    </div>
    <?php else: ?>

    <div class="row g-3">
      <?php while ($b = $bookings->fetch_assoc()):
        $isPast     = strtotime($b['appointment_date']) < strtotime('today');
        $canCancel  = in_array($b['status'], ['pending','confirmed']) && !$isPast;
        $statusClass = 'badge-' . $b['status'];
      ?>
      <div class="col-12">
        <div class="booking-history-card <?= $isPast ? 'past' : '' ?>">
          <div class="bhc-left">
            <div class="bhc-ref"><?= htmlspecialchars($b['reference_no']) ?></div>
            <div class="bhc-date">
              <i class="fas fa-calendar-day me-1"></i>
              <?= date('l, F j, Y', strtotime($b['appointment_date'])) ?>
              &nbsp;·&nbsp;
              <?= date('h:i A', strtotime($b['start_time'])) ?>
            </div>
            <div class="bhc-services"><?= htmlspecialchars($b['services'] ?? '—') ?></div>
            <div class="bhc-barber">
              <i class="fas fa-user-tie me-1"></i>
              <?= htmlspecialchars($b['bfname'] . ' ' . $b['blname']) ?>
            </div>
          </div>
          <div class="bhc-right">
            <span class="badge-status <?= $statusClass ?>"><?= ucfirst($b['status']) ?></span>
            <div class="bhc-price">₱<?= number_format($b['total_price'] ?? 0) ?></div>
            <?php if ($canCancel): ?>
            <form method="POST" onsubmit="return confirm('Cancel this appointment?')">
              <input type="hidden" name="action" value="cancel">
              <input type="hidden" name="appt_id" value="<?= $b['id'] ?>">
              <button type="submit" class="bhc-cancel-btn">
                <i class="fas fa-times me-1"></i>Cancel
              </button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>

    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>