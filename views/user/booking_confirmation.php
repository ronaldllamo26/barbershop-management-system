<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';

$ref = $conn->real_escape_string($_GET['ref'] ?? '');

if (!$ref) {
    header('Location: ' . BASE_PATH . 'index.php'); exit;
}

// Load appointment
$appt = $conn->query(
    "SELECT a.*,
            CONCAT(b.first_name,' ',b.last_name) AS barber_name,
            b.role_title AS barber_role,
            b.photo AS barber_photo,
            GROUP_CONCAT(s.name SEPARATOR ', ') AS services,
            SUM(aps.price_snapshot) AS total_price
     FROM appointments a
     LEFT JOIN barbers b ON b.id = a.barber_id
     LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
     LEFT JOIN services s ON s.id = aps.service_id
     WHERE a.reference_no = '$ref'
     GROUP BY a.id
     LIMIT 1"
)->fetch_assoc();

if (!$appt) {
    header('Location: ' . BASE_PATH . 'index.php'); exit;
}

// Load shop settings
$settingsRes = $conn->query("SELECT setting_key, setting_val FROM settings");
$settings = [];
while ($r = $settingsRes->fetch_assoc()) $settings[$r['setting_key']] = $r['setting_val'];

$pageTitle = 'Booking Confirmed';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

<section class="page-hero page-hero-sm">
  <div class="page-hero-overlay"></div>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center page-hero-content">
        <span class="sec-label">You're All Set!</span>
        <div class="divider center"></div>
        <h1 class="page-hero-title">Booking <em style="color:var(--gold)">Confirmed!</em></h1>
        <p class="page-hero-sub">Salamat! Your appointment has been received.</p>
      </div>
    </div>
  </div>
</section>

<section class="sec-pad bg-light-sec">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-7">

        <!-- Confirmation Card -->
        <div class="confirmation-card" id="printArea">

          <!-- Header -->
          <div class="cc-header">
            <div class="cc-logo">
              <span class="cc-logo-bg">BG</span>
              <div>
                <div class="cc-shop-name"><?= htmlspecialchars($settings['shop_name'] ?? 'BG Biglang Gwapo Barbershop') ?></div>
                <div class="cc-shop-addr"><?= htmlspecialchars($settings['shop_address'] ?? 'Quezon City, Metro Manila') ?></div>
              </div>
            </div>
            <div class="cc-status">
              <i class="fas fa-check-circle"></i>
              <span>Confirmed</span>
            </div>
          </div>

          <!-- Reference Number -->
          <div class="cc-ref">
            <div class="cc-ref-label">Reference Number</div>
            <div class="cc-ref-num"><?= htmlspecialchars($appt['reference_no']) ?></div>
            <div class="cc-ref-hint">Screenshot or print this for your records</div>
          </div>

          <!-- Appointment Details -->
          <div class="cc-details">
            <div class="cc-detail-row">
              <span><i class="fas fa-calendar-day"></i> Date</span>
              <strong><?= date('l, F j, Y', strtotime($appt['appointment_date'])) ?></strong>
            </div>
            <div class="cc-detail-row">
              <span><i class="fas fa-clock"></i> Time</span>
              <strong><?= date('h:i A', strtotime($appt['start_time'])) ?> – <?= date('h:i A', strtotime($appt['end_time'])) ?></strong>
            </div>
            <div class="cc-detail-row">
              <span><i class="fas fa-user-tie"></i> Barber</span>
              <strong><?= htmlspecialchars($appt['barber_name']) ?></strong>
            </div>
            <div class="cc-detail-row">
              <span><i class="fas fa-cut"></i> Service(s)</span>
              <strong><?= htmlspecialchars($appt['services'] ?? '—') ?></strong>
            </div>
            <div class="cc-detail-row">
              <span><i class="fas fa-receipt"></i> Total</span>
              <strong style="color:var(--gold-d);font-size:1.1rem;">₱<?= number_format($appt['total_price'] ?? 0) ?></strong>
            </div>
          </div>

          <!-- Customer Info -->
          <div class="cc-customer">
            <div class="cc-detail-row">
              <span><i class="fas fa-user"></i> Name</span>
              <strong><?= htmlspecialchars($appt['guest_name']) ?></strong>
            </div>
            <div class="cc-detail-row">
              <span><i class="fas fa-phone"></i> Phone</span>
              <strong><?= htmlspecialchars($appt['guest_phone']) ?></strong>
            </div>
            <?php if ($appt['customer_notes']): ?>
            <div class="cc-detail-row">
              <span><i class="fas fa-sticky-note"></i> Notes</span>
              <strong><?= htmlspecialchars($appt['customer_notes']) ?></strong>
            </div>
            <?php endif; ?>
          </div>

          <!-- Important Notes -->
          <div class="cc-notes">
            <div class="cc-notes-title"><i class="fas fa-info-circle me-2"></i>Important Reminders</div>
            <ul>
              <li>Please arrive <strong>5 minutes early</strong> for your appointment.</li>
              <li>To cancel or reschedule, please contact us at least <strong>2 hours before</strong> your appointment.</li>
              <li>Contact us: <strong><?= htmlspecialchars($settings['shop_phone'] ?? '+63 912 345 6789') ?></strong></li>
            </ul>
          </div>

          <!-- Footer -->
          <div class="cc-footer">
            <span><?= htmlspecialchars($settings['shop_name'] ?? 'BG Biglang Gwapo Barbershop') ?></span>
            <span><?= date('M j, Y \a\t h:i A', strtotime($appt['created_at'])) ?></span>
          </div>

        </div>

        <!-- Action Buttons -->
        <div class="d-flex gap-3 justify-content-center mt-4 flex-wrap no-print">
          <button onclick="window.print()" class="btn-gold">
            <i class="fas fa-print me-2"></i> Print / Save PDF
          </button>
          <?php if (!empty($_SESSION['customer_id'])): ?>
          <a href="/bg-barbershop/views/user/my_bookings.php" class="btn-black">
            <i class="fas fa-list me-2"></i> My Bookings
          </a>
          <?php else: ?>
          <a href="/bg-barbershop/views/user/register.php" class="btn-black">
            <i class="fas fa-user-plus me-2"></i> Create Account
          </a>
          <?php endif; ?>
          <a href="/bg-barbershop/index.php" class="btn-outline-dark">
            <i class="fas fa-home me-2"></i> Back to Home
          </a>
        </div>

      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>