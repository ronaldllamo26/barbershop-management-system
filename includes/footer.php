<!-- ======== FOOTER ======== -->
<footer id="footer">
  <div class="container">
    <div class="row g-5 pb-5">

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
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
          <a href="#" aria-label="Viber"><i class="fab fa-viber"></i></a>
        </div>
      </div>

      <div class="col-6 col-lg-2">
        <p class="footer-heading">Navigate</p>
        <ul class="footer-list">
          <li><a href="#hero">Home</a></li>
          <li><a href="#about">About Us</a></li>
          <li><a href="#services">Services</a></li>
          <li><a href="#barbers">Our Barbers</a></li>
          <li><a href="#gallery">Gallery</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </div>

      <div class="col-6 col-lg-3">
        <p class="footer-heading">Services</p>
        <ul class="footer-list">
          <li><a href="<?= BASE_PATH ?>views/user/services.php">Haircut &amp; Style</a></li>
          <li><a href="<?= BASE_PATH ?>views/user/services.php">Hot Towel Shave</a></li>
          <li><a href="<?= BASE_PATH ?>views/user/services.php">Beard Trim &amp; Shape</a></li>
          <li><a href="<?= BASE_PATH ?>views/user/services.php">Hair Treatment</a></li>
          <li><a href="<?= BASE_PATH ?>views/user/services.php">Kids Haircut</a></li>
          <li><a href="<?= BASE_PATH ?>views/user/services.php">BG Premium Package</a></li>
        </ul>
      </div>

      <div class="col-lg-3">
        <p class="footer-heading">Visit Us</p>
        <ul class="footer-list footer-info">
          <li><i class="fas fa-map-marker-alt"></i> 123 Sample Street,<br>Quezon City, Metro Manila</li>
          <li><i class="fas fa-phone-alt"></i> +63 912 345 6789</li>
          <li><i class="fas fa-envelope"></i> hello@bgbarbershop.com</li>
          <li><i class="fas fa-clock"></i> Mon–Fri: 9AM – 8PM<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sat–Sun: 9AM – 7PM</li>
        </ul>
      </div>

    </div>
  </div>

  <div class="footer-bottom">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
      <p>&copy; <?= date('Y') ?> <span>BG Biglang Gwapo Barbershop</span>. All rights reserved.</p>
      <p>
        <a href="#">Privacy Policy</a> &nbsp;·&nbsp;
        <a href="#">Terms of Service</a>
      </p>
    </div>
  </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Main JS -->
<script src="<?= BASE_PATH ?>assets/js/main.js"></script>
</body>
</html>
