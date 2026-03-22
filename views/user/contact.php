<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
$pageTitle = 'Contact Us';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

<!-- PAGE HERO -->
<section class="page-hero page-hero-sm">
  <div class="page-hero-overlay"></div>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center page-hero-content">
        <span class="sec-label">Get In Touch</span>
        <div class="divider center"></div>
        <h1 class="page-hero-title">Contact <em style="color:var(--gold)">Us</em></h1>
        <p class="page-hero-sub">We'd love to hear from you</p>
      </div>
    </div>
  </div>
</section>

<!-- CONTACT SECTION -->
<section class="sec-pad">
  <div class="container">
    <div class="row g-5">

      <!-- Left: Info -->
      <div class="col-lg-4 fade-up">
        <span class="sec-label">Find Us</span>
        <div class="divider"></div>
        <h2 class="sec-title mb-4">Visit Us in<br><em style="color:var(--gold-d)">Quezon City</em></h2>

        <div class="contact-card">
          <div class="cc-icon"><i class="fas fa-map-marker-alt"></i></div>
          <div class="cc-text">
            <h6>Address</h6>
            <p>213 Temple Street, Brgy. Balingasa,<br>Quezon City, Metro Manila</p>
          </div>
        </div>
        <div class="contact-card">
          <div class="cc-icon"><i class="fas fa-phone-alt"></i></div>
          <div class="cc-text">
            <h6>Phone / Viber</h6>
            <p>+63 912 345 6789<br>+63 900 123 4567</p>
          </div>
        </div>
        <div class="contact-card">
          <div class="cc-icon"><i class="fas fa-envelope"></i></div>
          <div class="cc-text">
            <h6>Email</h6>
            <p>hello@bgbarbershop.com</p>
          </div>
        </div>
        <div class="contact-card">
          <div class="cc-icon"><i class="fas fa-clock"></i></div>
          <div class="cc-text">
            <h6>Operating Hours</h6>
            <p>Mon – Fri &nbsp; 9:00 AM – 8:00 PM<br>Sat – Sun &nbsp; 9:00 AM – 7:00 PM</p>
          </div>
        </div>

        <div class="mt-4">
          <p class="sec-label mb-3">Follow Us</p>
          <div class="footer-socials">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-tiktok"></i></a>
            <a href="#"><i class="fab fa-viber"></i></a>
          </div>
        </div>
      </div>

      <!-- Right: Form + Map -->
      <div class="col-lg-8">

        <!-- Contact Form -->
        <div class="contact-form-card fade-up">
          <h3 class="contact-form-title">Send Us a Message</h3>
          <p class="contact-form-sub">Questions, feedback, or just want to say hi — we'll get back to you ASAP.</p>

          <div id="formAlert" style="display:none;"></div>

          <form id="contactForm" novalidate>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label-dark">Full Name *</label>
                <input type="text" name="name" class="input-dark" placeholder="Juan Dela Cruz" required>
              </div>
              <div class="col-md-6">
                <label class="form-label-dark">Phone *</label>
                <input type="tel" name="phone" class="input-dark" placeholder="+63 9XX XXX XXXX" required>
              </div>
              <div class="col-12">
                <label class="form-label-dark">Email (optional)</label>
                <input type="email" name="email" class="input-dark" placeholder="juan@email.com">
              </div>
              <div class="col-12">
                <label class="form-label-dark">Subject *</label>
                <select name="subject" class="input-dark" required>
                  <option value="" disabled selected>Select a subject</option>
                  <option>Booking Inquiry</option>
                  <option>Service Question</option>
                  <option>Feedback / Compliment</option>
                  <option>Complaint</option>
                  <option>Other</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label-dark">Message *</label>
                <textarea name="message" class="input-dark" rows="5" placeholder="Type your message here..." required></textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-gold w-100" id="contactBtn">
                  <i class="fas fa-paper-plane me-2"></i> Send Message
                </button>
              </div>
            </div>
          </form>
        </div>

        <!-- Map -->
        <div class="mt-4 fade-up">
          <iframe class="map-embed"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d123988.45!2d121.02300!3d14.67600!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b06fad951ab9%3A0x3c14eea12e2eefc1!2sQuezon%20City%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1700000000000"
            allowfullscreen="" loading="lazy" title="BG Barbershop Location">
          </iframe>
        </div>

      </div>
    </div>
  </div>
</section>

<script>
document.getElementById('contactForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const fd   = new FormData(this);
  const name = fd.get('name'), phone = fd.get('phone'), subject = fd.get('subject'), message = fd.get('message');
  const alertEl = document.getElementById('formAlert');

  if (!name || !phone || !subject || !message) {
    alertEl.style.display = 'block';
    alertEl.className = 'booking-alert';
    alertEl.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Please fill in all required fields.';
    return;
  }

  const btn = document.getElementById('contactBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';

  // Simulate send (replace with actual API call if needed)
  await new Promise(r => setTimeout(r, 1200));

  alertEl.style.display = 'block';
  alertEl.className = '';
  alertEl.style.cssText = 'background:#f0fdf4;border:1px solid #bbf7d0;border-left:4px solid #22c55e;color:#15803d;padding:14px 18px;font-size:.85rem;margin-bottom:20px;';
  alertEl.innerHTML = '<i class="fas fa-check-circle me-2"></i>Message sent! We\'ll get back to you shortly.';
  this.reset();
  btn.disabled = false;
  btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Message';
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>