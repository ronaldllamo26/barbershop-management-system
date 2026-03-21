<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
$pageTitle = 'About Us';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

<!-- PAGE HERO -->
<section class="page-hero">
  <div class="page-hero-overlay"></div>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center page-hero-content">
        <span class="sec-label">Who We Are</span>
        <div class="divider center"></div>
        <h1 class="page-hero-title">About <em style="color:var(--gold)">BG</em></h1>
        <p class="page-hero-sub">The story behind Biglang Gwapo Barbershop</p>
      </div>
    </div>
  </div>
</section>

<!-- STORY SECTION -->
<section class="sec-pad">
  <div class="container">
    <div class="row align-items-center g-5">

      <div class="col-lg-5 fade-up">
        <div class="about-img-outer">
          <div class="about-year-tag">
            <span class="ay-num">5+</span>
            <span class="ay-label">Years</span>
          </div>
          <img src="https://images.unsplash.com/photo-1585747860715-2ba37e788b70?w=800&q=85&fit=crop"
               alt="BG Barbershop Interior" class="about-img-main">
          <img src="https://images.unsplash.com/photo-1473679408190-0693dd26c96f?w=400&q=85&fit=crop"
               alt="Shop Detail" class="about-img-small">
        </div>
      </div>

      <div class="col-lg-6 offset-lg-1 fade-up">
        <span class="sec-label">Our Story</span>
        <div class="divider"></div>
        <h2 class="sec-title mb-4">
          More Than a Haircut —<br>
          <em style="color:var(--gold-d)">It's a Tradition.</em>
        </h2>
        <p style="color:var(--gray-d);line-height:1.9;margin-bottom:18px;">
          Biglang Gwapo Barbershop started with a simple idea — that every Filipino man deserves
          to feel his absolute best without having to fly abroad or spend a fortune. We opened our
          doors in Quezon City with one barber chair, one mirror, and a whole lot of passion.
        </p>
        <p style="color:var(--gray-d);line-height:1.9;margin-bottom:18px;">
          Today, BG has grown into one of QC's most trusted grooming destinations. Our team of
          master barbers brings years of experience and a genuine love for the craft to every
          single appointment — whether it's a quick clean-up or a full premium package.
        </p>
        <p style="color:var(--gray-d);line-height:1.9;">
          We believe grooming is not just about how you look — it's about how you carry yourself.
          Walk in, sit back, and walk out a better version of yourself. That's the BG promise.
        </p>
      </div>

    </div>
  </div>
</section>

<!-- STATS STRIP -->
<section class="sec-pad-sm bg-black-sec">
  <div class="container">
    <div class="row g-4 text-center">
      <?php
      $stats = [
        ['5+',    'Years in Business'],
        ['12+',   'Master Barbers'],
        ['5,000+','Happy Clients'],
        ['4.9',   'Average Rating'],
      ];
      foreach ($stats as [$num, $label]): ?>
      <div class="col-6 col-lg-3 fade-up">
        <div class="about-stat">
          <div class="about-stat-num"><?= $num ?></div>
          <div class="about-stat-label"><?= $label ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- VALUES -->
<section class="sec-pad bg-light-sec">
  <div class="container">
    <div class="row justify-content-center mb-5">
      <div class="col-lg-6 text-center fade-up">
        <span class="sec-label">What We Stand For</span>
        <div class="divider center"></div>
        <h2 class="sec-title">Our <em style="color:var(--gold-d)">Values</em></h2>
      </div>
    </div>
    <div class="row g-4">
      <?php
      $values = [
        ['fa-medal',       'Craftsmanship',    'Every cut is deliberate. Every line is clean. We take pride in the details others overlook.'],
        ['fa-heart',       'Passion',          'We don\'t just do this for a living — we do it because we genuinely love what we do.'],
        ['fa-users',       'Community',        'BG is more than a shop. It\'s a place where men in QC come to unwind, connect, and look sharp.'],
        ['fa-shield-alt',  'Cleanliness',      'Strict hygiene standards every session. Every tool sanitized. Every surface clean. Always.'],
        ['fa-star',        'Excellence',       'We don\'t settle for "pwede na." We aim for the best cut you\'ve had — every single time.'],
        ['fa-handshake',   'Respect',          'Your time matters. Your preferences matter. We listen, we deliver, and we earn your trust.'],
      ];
      foreach ($values as $i => [$icon, $title, $desc]): ?>
      <div class="col-md-6 col-lg-4 fade-up d<?= ($i%3)+1 ?>">
        <div class="value-card">
          <div class="value-icon"><i class="fas <?= $icon ?>"></i></div>
          <h4 class="value-title"><?= $title ?></h4>
          <p class="value-desc"><?= $desc ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- TEAM -->
<section class="sec-pad">
  <div class="container">
    <div class="row justify-content-center mb-5">
      <div class="col-lg-6 text-center fade-up">
        <span class="sec-label">The People Behind BG</span>
        <div class="divider center"></div>
        <h2 class="sec-title">Meet the <em style="color:var(--gold-d)">Team</em></h2>
      </div>
    </div>
    <div class="row g-4">
      <?php
      $team = [
        ['https://images.unsplash.com/photo-1599351431202-1e0f0137899a?w=600&h=700&q=85&fit=crop', 'Marco Reyes',   'Head Barber',         'Specializes in fades and textured styles. 8 years of experience.'],
        ['https://images.unsplash.com/photo-1622286342621-4bd786c2447c?w=600&h=700&q=85&fit=crop', 'Jake Santos',   'Senior Barber',        'Master of classic cuts and hot towel shaves. 6 years in the craft.'],
        ['https://images.unsplash.com/photo-1503443207922-dff7d543fd0e?w=600&h=700&q=85&fit=crop', 'Carlo Mendoza', 'Fade Specialist',      'If you want the cleanest fade in QC — Carlo is your guy.'],
        ['https://images.unsplash.com/photo-1534297635766-a262cdcb8ee4?w=600&h=700&q=85&fit=crop', 'Luis Garcia',   'Color & Style Expert', 'Brings modern color techniques and creative styling to every client.'],
      ];
      foreach ($team as $i => [$photo, $name, $role, $bio]): ?>
      <div class="col-md-6 col-lg-3 fade-up d<?= ($i%4)+1 ?>">
        <div class="team-card">
          <div class="team-img-wrap">
            <img src="<?= $photo ?>" alt="<?= $name ?>">
          </div>
          <div class="team-info">
            <h4><?= $name ?></h4>
            <span class="team-role"><?= $role ?></span>
            <p class="team-bio"><?= $bio ?></p>
            <div class="team-socials">
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

<!-- CTA -->
<section class="sec-pad-sm bg-black-sec">
  <div class="container">
    <div class="row align-items-center justify-content-between g-4">
      <div class="col-lg-7">
        <span class="sec-label">Ready?</span>
        <h2 class="sec-title white mt-2 mb-0">Experience BG <em style="color:var(--gold)">for yourself.</em></h2>
      </div>
      <div class="col-lg-auto">
        <a href="<?= BASE_PATH ?>views/user/booking.php" class="btn-gold">
          <i class="fas fa-calendar-check"></i> Book Now
        </a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>