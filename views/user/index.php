<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', basename(dirname(dirname(dirname(__FILE__)))) . '/');
}
$pageTitle = 'Home';

// Real Unsplash photo URLs — browser loads directly, no download needed
$photos = [
    'hero_bg'    => 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=1920&q=80&fit=crop',
    'hero_main'  => 'https://images.unsplash.com/photo-1621605815971-fbc98d665033?w=900&q=85&fit=crop',
    'about_main' => 'https://images.unsplash.com/photo-1585747860715-2ba37e788b70?w=800&q=85&fit=crop',
    'barber_1'   => 'https://images.unsplash.com/photo-1599351431202-1e0f0137899a?w=600&h=700&q=85&fit=crop',
    'barber_2'   => 'https://images.unsplash.com/photo-1622286342621-4bd786c2447c?w=600&h=700&q=85&fit=crop',
    'barber_3'   => 'https://images.unsplash.com/photo-1503443207922-dff7d543fd0e?w=600&h=700&q=85&fit=crop',
    'barber_4'   => 'https://images.unsplash.com/photo-1534297635766-a262cdcb8ee4?w=600&h=700&q=85&fit=crop',
    'g1'         => 'https://images.unsplash.com/photo-1605497788044-5a32c7078486?w=900&q=85&fit=crop',
    'g2'         => 'https://images.unsplash.com/photo-1621605815971-fbc98d665033?w=600&q=85&fit=crop',
    'g3'         => 'https://images.unsplash.com/photo-1560869713-7d0a29430803?w=600&q=85&fit=crop',
    'g4'         => 'https://images.unsplash.com/photo-1519345182560-3f2917c472ef?w=600&q=85&fit=crop',
    'g5'         => 'https://images.unsplash.com/photo-1596728325488-58c87691e9af?w=600&q=85&fit=crop',
    'about_sm'   => 'https://images.unsplash.com/photo-1473679408190-0693dd26c96f?w=400&q=85&fit=crop',
];

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

<!-- ═══════ HERO ═══════ -->
<section id="hero">
  <div class="hero-bg-img" style="background-image:url('<?= $photos['hero_bg'] ?>');"></div>
  <div class="hero-overlay"></div>
  <div class="container">
    <div class="row align-items-center min-vh-100 py-5">

      <div class="col-lg-6 hero-content">
        <div class="hero-eyebrow">Quezon City's Premier Barbershop</div>
        <h1 class="hero-title">
          Look Sharp.
          <span class="ht-gold">Feel Unstoppable.</span>
        </h1>
        <p class="hero-desc">
          More than a haircut — it's an experience. Walk in, sit back,
          and walk out as the best version of yourself.
        </p>
        <div class="hero-cta">
          <a href="<?= BASE_PATH ?>views/user/booking.php" class="btn-gold">
            <i class="fas fa-calendar-check"></i> Book Appointment
          </a>
          <a href="#services" class="btn-outline-gold">Our Services</a>
        </div>
        <div class="hero-stats">
          <div>
            <div class="stat-num" data-target="1" data-suffix="+">0+</div>
            <div class="stat-label">Years of Service</div>
          </div>
          <div>
            <div class="stat-num" data-target="5" data-suffix="+">0+</div>
            <div class="stat-label">Master Barbers</div>
          </div>
          <div>
            <div class="stat-num" data-target="1200" data-suffix="+">0+</div>
            <div class="stat-label">Happy Clients</div>
          </div>
        </div>
      </div>

      <div class="col-lg-5 offset-lg-1">
        <div class="hero-img-wrap">
          <img src="<?= $photos['hero_main'] ?>" alt="Premium barbershop experience">
          <div class="hero-badge">
            <span class="hb-num">#1</span>
            <span class="hb-text">In QC</span>
          </div>
        </div>
      </div>

    </div>
  </div>
  
</section>


<!-- ═══════ MARQUEE ═══════ -->
<div class="marquee-wrap">
  <div class="marquee-track">
    <span class="marquee-item"><span class="marquee-dot"></span>Haircut &amp; Style</span>
    <span class="marquee-item"><span class="marquee-dot"></span>Hot Towel Shave</span>
    <span class="marquee-item"><span class="marquee-dot"></span>Beard Grooming</span>
    <span class="marquee-item"><span class="marquee-dot"></span>Hair Treatment</span>
    <span class="marquee-item"><span class="marquee-dot"></span>Online Booking</span>
    <span class="marquee-item"><span class="marquee-dot"></span>Walk-in Welcome</span>
    <span class="marquee-item"><span class="marquee-dot"></span>Quezon City</span>
    <span class="marquee-item"><span class="marquee-dot"></span>Premium Experience</span>
  </div>
</div>


<!-- ═══════ ABOUT ═══════ -->
<section id="about" class="sec-pad">
  <div class="container">
    <div class="row align-items-center g-5">

      <div class="col-lg-5 fade-up">
        <div class="about-img-outer">
          <div class="about-year-tag">
            <span class="ay-num">1+</span>
            <span class="ay-label">Years</span>
          </div>
          <img src="<?= $photos['about_main'] ?>" alt="BG Barbershop interior" class="about-img-main">
          <img src="<?= $photos['about_sm'] ?>" alt="Barbering in action" class="about-img-small">
        </div>
      </div>

      <div class="col-lg-6 offset-lg-1">
        <div class="fade-up">
          <span class="sec-label">About BG</span>
          <div class="divider"></div>
          <h2 class="sec-title mb-3">
            More Than a Haircut —<br>
            <em style="color:var(--gold-d);font-style:italic">It's a Tradition.</em>
          </h2>
          <p class="sec-sub mb-4">
            Biglang Gwapo Barbershop was built on one belief: every man deserves to feel his best.
            We bring world-class grooming to Quezon City — precision cuts, premium products,
            and a truly relaxed experience.
          </p>
        </div>
        <div class="fade-up d1">
          <?php
          $features = [
            ['fa-medal',        'Master Barbers Only',       'Every BG barber is trained, certified, and passionate about the craft.'],
            ['fa-leaf',         'Premium Products',          'Top-tier grooming products — gentle on hair, powerful on results.'],
            ['fa-calendar-check','Easy Online Booking',      'Reserve your slot in minutes. No waiting, no hassle — just show up.'],
            ['fa-shield-alt',   'Always Clean & Sanitized',  'Strict hygiene standards every session, every tool, every time.'],
          ];
          foreach ($features as [$icon, $title, $desc]): ?>
          <div class="feature-row">
            <div class="feat-icon"><i class="fas <?= $icon ?>"></i></div>
            <div class="feat-text">
              <h5><?= $title ?></h5>
              <p><?= $desc ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="fade-up d2 d-flex flex-wrap gap-3 mt-2">
          <a href="#services" class="btn-black">Explore Services</a>
          <a href="#barbers" class="btn-outline-dark">Meet the Team</a>
        </div>
      </div>

    </div>
  </div>
</section>


<!-- ═══════ SERVICES ═══════ -->
<section id="services" class="sec-pad bg-light-sec">
  <div class="container">
    <div class="row justify-content-center mb-5">
      <div class="col-lg-6 text-center fade-up">
        <span class="sec-label">What We Offer</span>
        <div class="divider center"></div>
        <h2 class="sec-title">Premium Services<br><em style="color:var(--gold-d)">For the Modern Man</em></h2>
      </div>
    </div>
    <div class="row g-4">
      <?php
      if (!isset($conn)) require_once __DIR__ . '/../../config/db.php';
      $svcIcons = [
        'Haircut & Style' => 'fa-cut',
        'Shave'           => 'fa-fire',
        'Beard'           => 'fa-user-tie',
        'Treatment'       => 'fa-spa',
        'Kids'            => 'fa-child',
        'Packages'        => 'fa-star',
      ];
      $catsRes = $conn->query("SELECT * FROM service_categories ORDER BY sort_order LIMIT 6");
      $i = 0;
      while ($cat = $catsRes->fetch_assoc()):
        $catId   = $cat['id'];
        $catName = $cat['name'];
        $icon    = $svcIcons[$catName] ?? 'fa-scissors';
        // Get min price for this category
        $priceRes = $conn->query("SELECT MIN(price) AS min_price FROM services WHERE category_id=$catId AND is_active=1 LIMIT 1");
        $priceRow = $priceRes ? $priceRes->fetch_assoc() : null;
        $price    = ($priceRow && $priceRow['min_price']) ? '₱' . number_format($priceRow['min_price']) : '₱0';
        $desc     = $cat['description'] ?? '';
        $num      = str_pad($i+1, 2, '0', STR_PAD_LEFT);
      ?>
      <div class="col-md-6 col-lg-4 fade-up d<?= ($i%3)+1 ?>">
        <div class="svc-card">
          <div class="svc-num"><?= $num ?></div>
          <div class="svc-icon"><i class="fas <?= $icon ?>"></i></div>
          <h4><?= htmlspecialchars($catName) ?></h4>
          <p><?= htmlspecialchars($desc) ?></p>
          <div class="svc-price"><?= $price ?> <small>&amp; up</small></div>
        </div>
      </div>
      <?php $i++; endwhile; ?>
    </div>
    <div class="text-center mt-5 fade-up">
      <a href="<?= BASE_PATH ?>views/user/services.php" class="btn-outline-dark">View Full Price List</a>
    </div>
  </div>
</section>


<!-- ═══════ BARBERS ═══════ -->
<section id="barbers" class="sec-pad bg-black-sec">
  <div class="container">
    <div class="row justify-content-center mb-5">
      <div class="col-lg-6 text-center fade-up">
        <span class="sec-label">The Team</span>
        <div class="divider center"></div>
        <h2 class="sec-title white">Meet Our<br><em style="color:var(--gold)">Master Barbers</em></h2>
        <p class="sec-sub white mx-auto mt-3">
          Each barber at BG is a craftsman — trained to listen, skilled to deliver,
          and passionate about making you look your best.
        </p>
      </div>
    </div>
    <div class="row g-4">
      <?php
      if (!isset($conn)) require_once __DIR__ . '/../../config/db.php';
      $barbersRes = $conn->query("SELECT * FROM barbers WHERE is_active=1 ORDER BY sort_order, id LIMIT 4");
      $i = 0;
      while ($b = $barbersRes->fetch_assoc()):
        $bname  = htmlspecialchars($b['first_name'] . ' ' . $b['last_name']);
        $brole  = htmlspecialchars($b['role_title']);
        $bphoto = htmlspecialchars($b['photo'] ?? '');
        $bInsta = htmlspecialchars($b['instagram'] ?? '#');
        $bFace  = htmlspecialchars($b['facebook']  ?? '#');
      ?>
      <div class="col-md-6 col-lg-3 fade-up d<?= $i+1 ?>">
        <div class="barber-card">
          <?php if ($bphoto): ?>
          <img src="<?= $bphoto ?>" alt="<?= $bname ?>" class="barber-img">
          <?php else: ?>
          <div class="barber-img" style="background:var(--dark-2);display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-user-tie" style="font-size:4rem;color:var(--gold);"></i>
          </div>
          <?php endif; ?>
          <div class="barber-overlay"></div>
          <div class="barber-info">
            <h4><?= $bname ?></h4>
            <span class="barber-role"><?= $brole ?></span>
            <div class="barber-socials">
              <a href="<?= $bInsta ?>" <?= $bInsta !== '#' ? 'target="_blank"' : '' ?>><i class="fab fa-instagram"></i></a>
              <a href="<?= $bFace ?>"  <?= $bFace  !== '#' ? 'target="_blank"' : '' ?>><i class="fab fa-facebook-f"></i></a>
            </div>
          </div>
        </div>
      </div>
      <?php $i++; endwhile; ?>
    </div>
  </div>
</section>


<!-- ═══════ GALLERY ═══════ -->
<section id="gallery" class="sec-pad">
  <div class="container">
    <div class="row justify-content-between align-items-end mb-4">
      <div class="col-lg-5 fade-up">
        <span class="sec-label">The Work</span>
        <div class="divider"></div>
        <h2 class="sec-title">Our <em style="color:var(--gold-d)">Gallery</em></h2>
      </div>
      <div class="col-auto fade-up">
        <a href="<?= BASE_PATH ?>views/user/gallery.php" class="btn-outline-dark">View All Photos</a>
      </div>
    </div>
    <div class="gal-grid fade-up">
      <?php foreach ([$photos['g1'],$photos['g2'],$photos['g3'],$photos['g4'],$photos['g5']] as $i => $url): ?>
      <div class="gal-item">
        <img src="<?= $url ?>" alt="BG Barbershop work <?= $i+1 ?>">
        <div class="gal-hover"><i class="fas fa-expand-alt"></i></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<!-- ═══════ TESTIMONIALS ═══════ -->
<section id="testimonials" class="sec-pad bg-light-sec">
  <div class="container">
    <div class="row justify-content-center mb-5">
      <div class="col-lg-6 text-center fade-up">
        <span class="sec-label">Client Reviews</span>
        <div class="divider center"></div>
        <h2 class="sec-title">What Our<br><em style="color:var(--gold-d)">Clients Say</em></h2>
      </div>
    </div>
    <div class="row g-4">
      <?php
      if (!isset($conn)) require_once __DIR__ . '/../../config/db.php';
      $reviewsRes = $conn->query("SELECT * FROM testimonials WHERE is_active=1 AND is_featured=1 ORDER BY created_at DESC LIMIT 3");
      $reviewCount = 0;
      if ($reviewsRes && $reviewsRes->num_rows > 0):
        while ($rev = $reviewsRes->fetch_assoc()):
          $reviewCount++;
      ?>
      <div class="col-md-6 col-lg-4 fade-up d<?= $reviewCount ?>">
        <div class="testi-card">
          <span class="testi-quote">"</span>
          <div class="testi-stars">
            <?= str_repeat('<i class="fas fa-star"></i>', $rev['rating']) ?>
          </div>
          <p class="testi-text">"<?= htmlspecialchars($rev['review_text']) ?>"</p>
          <div class="testi-name"><?= htmlspecialchars($rev['customer_name']) ?></div>
          <div class="testi-meta"><?= htmlspecialchars($rev['location'] ?: 'BG Customer') ?></div>
        </div>
      </div>
      <?php endwhile;
      else: ?>
      <div class="col-12 text-center" style="color:var(--gray-l);padding:40px 0;">
        <p>No reviews yet. Be the first to share your experience!</p>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>


<!-- ═══════ CTA ═══════ -->
<section id="cta" class="sec-pad bg-black-sec">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-7 text-center fade-up">
        <span class="sec-label">Ready to Look Your Best?</span>
        <div class="divider center"></div>
        <h2 class="sec-title white mb-3">
          Book Your Appointment<br>
          <em style="color:var(--gold)">Today.</em>
        </h2>
        <p class="sec-sub white mx-auto mb-5">
          Don't wait in line. Reserve your slot in under a minute and
          walk in ready for your transformation.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
          <a href="<?= BASE_PATH ?>views/user/booking.php" class="btn-gold">
            <i class="fas fa-calendar-check"></i> Book Now
          </a>
          <a href="#contact" class="btn-outline-gold">Find Us</a>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ═══════ CONTACT ═══════ -->
<section id="contact" class="sec-pad">
  <div class="container">
    <div class="row justify-content-center mb-5">
      <div class="col-lg-5 text-center fade-up">
        <span class="sec-label">Get In Touch</span>
        <div class="divider center"></div>
        <h2 class="sec-title">Visit Us in<br><em style="color:var(--gold-d)">Quezon City</em></h2>
      </div>
    </div>
    <div class="row g-4 align-items-start">
      <div class="col-lg-4 fade-up d1">
        <?php
        $contacts = [
          ['fa-map-marker-alt', 'Address',         '213 Temple Street, Brgy. Balingasa,<br>Quezon City, Metro Manila'],
          ['fa-phone-alt',      'Phone / Viber',   '+63 912 345 6789<br>+63 900 123 4567'],
          ['fa-clock',          'Operating Hours', 'Mon – Fri &nbsp; 9:00 AM – 8:00 PM<br>Sat – Sun &nbsp; 9:00 AM – 7:00 PM'],
          ['fab fa-facebook-f', 'Social Media',    '@BGBiglangGwapoBarbershop'],
        ];
        foreach ($contacts as [$icon, $label, $val]): ?>
        <div class="contact-card">
          <div class="cc-icon"><i class="<?= (str_starts_with($icon,'fab') ? '' : 'fas ') . $icon ?>"></i></div>
          <div class="cc-text"><h6><?= $label ?></h6><p><?= $val ?></p></div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="col-lg-8 fade-up d2">
        <iframe class="map-embed"
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d123988.45!2d121.02300!3d14.67600!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b06fad951ab9%3A0x3c14eea12e2eefc1!2sQuezon%20City%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1700000000000"
          allowfullscreen="" loading="lazy" title="BG Barbershop Location">
        </iframe>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>