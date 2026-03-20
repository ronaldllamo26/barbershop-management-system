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
    'barber_1' => '/bg-barbershop/assets/images/barbers/barber-1.jpg',
    'barber_2'   => 'https://images.unsplash.com/photo-1622286342621-4bd786c2447c?w=600&h=700&q=85&fit=crop',
    'barber_3' => '/bg-barbershop/assets/images/barbers/barber-3.jpg',
    'barber_4'   => 'https://images.unsplash.com/photo-1534297635766-a262cdcb8ee4?w=600&h=700&q=85&fit=crop',
    'g1'         => 'https://images.unsplash.com/photo-1605497788044-5a32c7078486?w=900&q=85&fit=crop',
    'g2' => 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=600&q=85&fit=crop',
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
            <div class="stat-num" data-target="6" data-suffix="+">0+</div>
            <div class="stat-label">Master Barbers</div>
          </div>
          <div>
            <div class="stat-num" data-target="5000" data-suffix="+">0+</div>
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
  <div class="hero-scroll">
    <span class="scroll-text">Scroll</span>
    <div class="scroll-line"></div>
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
      $services = [
        ['01','fa-cut',        'Haircut &amp; Style',       'Classic or modern, fade or taper — precision cuts tailored to your face shape.', '₱350','& up'],
        ['02','fa-fire',       'Hot Towel Shave',           'Classic straight-razor shave with hot towel and premium shaving cream.',         '₱450','& up'],
        ['03','fa-user-tie',   'Beard Trim &amp; Shape',    'From a clean shave to a sculpted beard — shaped to complement your look.',       '₱250','& up'],
        ['04','fa-spa',        'Hair &amp; Scalp Treatment','Nourishing treatments — anti-dandruff, deep conditioning, hot oil.',             '₱500','& up'],
        ['05','fa-child',      'Kids Haircut',              'Gentle and fun cuts for the little ones. Every kid leaves feeling gwapo.',       '₱250','& up'],
        ['06','fa-star',       'BG Premium Package',        'Haircut + Hot Towel Shave + Beard Trim + Scalp Massage. The full glow-up.',     '₱999','complete'],
      ];
      foreach ($services as $i => [$num, $icon, $name, $desc, $price, $suffix]): ?>
      <div class="col-md-6 col-lg-4 fade-up d<?= ($i%3)+1 ?>">
        <div class="svc-card">
          <div class="svc-num"><?= $num ?></div>
          <div class="svc-icon"><i class="fas <?= $icon ?>"></i></div>
          <h4><?= $name ?></h4>
          <p><?= $desc ?></p>
          <div class="svc-price"><?= $price ?> <small><?= $suffix ?></small></div>
        </div>
      </div>
      <?php endforeach; ?>
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
      $barbers = [
        [$photos['barber_1'], 'Lebron James',   'Head Barber'],
        [$photos['barber_2'], 'Junmar Fajardo',   'Senior Barber'],
        [$photos['barber_3'], 'Xi Jinping', 'Fade Specialist'],
        [$photos['barber_4'], 'Adolf Hitler',   'Color &amp; Style Expert'],
      ];
      foreach ($barbers as $i => [$photo, $name, $role]): ?>
      <div class="col-md-6 col-lg-3 fade-up d<?= $i+1 ?>">
        <div class="barber-card">
          <img src="<?= $photo ?>" alt="<?= $name ?>" class="barber-img">
          <div class="barber-overlay"></div>
          <div class="barber-info">
            <h4><?= $name ?></h4>
            <span class="barber-role"><?= $role ?></span>
            <div class="barber-socials">
              <a href="#"><i class="fab fa-instagram"></i></a>
              <a href="#"><i class="fab fa-facebook-f"></i></a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
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
      $reviews = [
        ['Jerico P.', 'Regular Client · Cubao',   5, 'Best barbershop in QC, walang tanong. Marco knows exactly what I want without me even explaining. Consistent — talo pa yung mga kilala.'],
        ['Aldrin M.', 'Regular Client · QC',      5, 'Dali mag-book, malinis yung lugar, at yung barbers professional talaga. Worth it ang presyo — babalik ka talaga.'],
        ['Renzo D.',  'New Client · Diliman',     5, 'First time ko dito, sinabihan ako ng friend ko. Hindi ako disappointed. Yung hot towel shave experience, ibang klase talaga.'],
      ];
      foreach ($reviews as $i => [$name, $meta, $stars, $text]): ?>
      <div class="col-md-6 col-lg-4 fade-up d<?= $i+1 ?>">
        <div class="testi-card">
          <span class="testi-quote">"</span>
          <div class="testi-stars"><?= str_repeat('<i class="fas fa-star"></i>', $stars) ?></div>
          <p class="testi-text">"<?= $text ?>"</p>
          <div class="testi-name"><?= $name ?></div>
          <div class="testi-meta"><?= $meta ?></div>
        </div>
      </div>
      <?php endforeach; ?>
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
          ['fa-map-marker-alt', 'Address',         '69 Tawi-Tawi, Brgy. Balingasa,<br>Quezon City, Metro Manila'],
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
