<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Load settings from DB for footer info
if (!isset($conn)) require_once __DIR__ . '/../config/db.php';
$footerSettings = [];
$fsRes = $conn->query("SELECT setting_key, setting_val FROM settings");
if ($fsRes) while ($r = $fsRes->fetch_assoc()) $footerSettings[$r['setting_key']] = $r['setting_val'];
$fs = function($key, $default='') use ($footerSettings) {
    return htmlspecialchars($footerSettings[$key] ?? $default);
};
?>
<!-- ======== FOOTER ======== -->
<footer id="footer">
  <div class="container">
    <div class="row g-5 pb-5">

      <!-- Brand + Social -->
      <div class="col-lg-4">
        <div class="footer-brand">
          <span class="footer-logo-bg">BG</span>
          <span class="footer-logo-name">Biglang Gwapo <em>Barbershop</em></span>
        </div>
        <p class="footer-desc mt-3">
          Premium grooming for the modern Filipino gentleman.
          Look sharp, feel unstoppable — every single visit.
        </p>
        <div class="footer-socials mt-4">
          <?php if ($footerSettings['facebook_url'] ?? ''): ?>
          <a href="<?= $fs('facebook_url') ?>" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <?php else: ?>
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <?php endif; ?>
          <?php if ($footerSettings['instagram_url'] ?? ''): ?>
          <a href="<?= $fs('instagram_url') ?>" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <?php else: ?>
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <?php endif; ?>
          <?php if ($footerSettings['tiktok_url'] ?? ''): ?>
          <a href="<?= $fs('tiktok_url') ?>" target="_blank" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
          <?php else: ?>
          <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
          <?php endif; ?>
          <a href="#" aria-label="Viber"><i class="fab fa-viber"></i></a>
        </div>
      </div>

      <!-- Navigate -->
      <div class="col-6 col-lg-2">
        <p class="footer-heading">Navigate</p>
        <ul class="footer-list">
          <li><a href="/bg-barbershop/index.php">Home</a></li>
          <li><a href="/bg-barbershop/views/user/about.php">About Us</a></li>
          <li><a href="/bg-barbershop/views/user/services.php">Services</a></li>
          <li><a href="/bg-barbershop/index.php#barbers">Our Barbers</a></li>
          <li><a href="/bg-barbershop/views/user/gallery.php">Gallery</a></li>
          <li><a href="/bg-barbershop/views/user/contact.php">Contact</a></li>
        </ul>
      </div>

      <!-- Services -->
      <div class="col-6 col-lg-3">
        <p class="footer-heading">Services</p>
        <ul class="footer-list">
          <li><a href="/bg-barbershop/views/user/services.php">Haircut &amp; Style</a></li>
          <li><a href="/bg-barbershop/views/user/services.php">Hot Towel Shave</a></li>
          <li><a href="/bg-barbershop/views/user/services.php">Beard Trim &amp; Shape</a></li>
          <li><a href="/bg-barbershop/views/user/services.php">Hair Treatment</a></li>
          <li><a href="/bg-barbershop/views/user/services.php">Kids Haircut</a></li>
          <li><a href="/bg-barbershop/views/user/booking.php">Book Appointment</a></li>
        </ul>

        <!-- Account links -->
        <p class="footer-heading mt-4">Account</p>
        <ul class="footer-list">
          <?php if (!empty($_SESSION['customer_id'])): ?>
          <li><a href="/bg-barbershop/views/user/my_bookings.php"><i class="fas fa-calendar-check me-1"></i>My Bookings</a></li>
          <li><a href="/bg-barbershop/views/user/logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
          <?php else: ?>
          <li><a href="/bg-barbershop/views/user/login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a></li>
          <li><a href="/bg-barbershop/views/user/register.php"><i class="fas fa-user-plus me-1"></i>Register</a></li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Visit Us -->
      <div class="col-lg-3">
        <p class="footer-heading">Visit Us</p>
        <ul class="footer-list footer-info">
          <li>
            <i class="fas fa-map-marker-alt"></i>
            <?= $fs('shop_address', '213 Temple Street, Quezon City, Metro Manila') ?>
          </li>
          <li>
            <i class="fas fa-phone-alt"></i>
            <?= $fs('shop_phone', '+63 912 345 6789') ?>
          </li>
          <li>
            <i class="fas fa-envelope"></i>
            <?= $fs('shop_email', 'hello@bgbarbershop.com') ?>
          </li>
          <li>
            <i class="fas fa-clock"></i>
            Mon–Fri: <?= $fs('shop_hours_weekday', '9AM – 8PM') ?><br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sat–Sun: <?= $fs('shop_hours_weekend', '9AM – 7PM') ?>
          </li>
        </ul>
      </div>

    </div>
  </div>

  <div class="footer-bottom">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
      <p>&copy; <?= date('Y') ?> <span><?= $fs('shop_name', 'BG Biglang Gwapo Barbershop') ?></span>. All rights reserved.</p>
      <p>
        <a href="#">Privacy Policy</a> &nbsp;·&nbsp;
        <a href="#">Terms of Service</a>
      </p>
    </div>
  </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- Main JS -->
<script src="/bg-barbershop/assets/js/main.js"></script>
</body>
</html>