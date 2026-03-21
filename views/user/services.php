<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/bg-barbershop/');
}
$pageTitle = 'Services';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

<!-- PAGE HERO -->
<section class="page-hero">
  <div class="page-hero-overlay"></div>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center page-hero-content">
        <span class="sec-label">What We Offer</span>
        <div class="divider center"></div>
        <h1 class="page-hero-title">Our <em style="color:var(--gold)">Services</em></h1>
        <p class="page-hero-sub">Premium grooming for the modern Filipino gentleman</p>
      </div>
    </div>
  </div>
</section>

<!-- SERVICES FULL LIST -->
<section class="sec-pad bg-white-sec">
  <div class="container">

    <?php
    $categories = [
      [
        'icon'  => 'fa-cut',
        'name'  => 'Haircut & Style',
        'desc'  => 'From classic cuts to modern fades — our barbers deliver precision every time.',
        'items' => [
          ['Regular Haircut',    'Classic cut with your choice of style.',              '₱350', '30 mins'],
          ['Skin Fade',          'High, mid, or low fade — clean and precise.',         '₱400', '35 mins'],
          ['Textured Crop',      'Modern crop with textured top.',                      '₱400', '35 mins'],
          ['Haircut + Blowdry',  'Full cut and professional blowdry finish.',           '₱500', '45 mins'],
        ],
      ],
      [
        'icon'  => 'fa-fire',
        'name'  => 'Shave',
        'desc'  => 'The classic barbershop shave experience — relaxing, precise, premium.',
        'items' => [
          ['Hot Towel Shave', 'Full straight-razor shave with hot towel treatment.',  '₱450', '30 mins'],
          ['Clean Shave',     'Quick clean shave with premium shaving cream.',        '₱250', '20 mins'],
        ],
      ],
      [
        'icon'  => 'fa-user-tie',
        'name'  => 'Beard',
        'desc'  => 'Shape, sculpt, and define your beard to perfection.',
        'items' => [
          ['Beard Trim & Shape',   'Clean up and sculpt to your desired style.',      '₱250', '20 mins'],
          ['Beard Trim + Line Up', 'Full beard service with detailed line-up.',       '₱350', '30 mins'],
        ],
      ],
      [
        'icon'  => 'fa-spa',
        'name'  => 'Hair & Scalp Treatment',
        'desc'  => 'Restore and nourish your hair with our premium treatment services.',
        'items' => [
          ['Hot Oil Treatment',       'Deep conditioning with premium hair oil.',          '₱350', '30 mins'],
          ['Scalp Massage',           'Relaxing scalp massage to relieve stress.',         '₱300', '20 mins'],
          ['Anti-Dandruff Treatment', 'Medicated scalp treatment.',                        '₱400', '30 mins'],
          ['Hair Color (Basic)',      'Single-process color application.',                 '₱800', '60 mins'],
        ],
      ],
      [
        'icon'  => 'fa-child',
        'name'  => 'Kids',
        'desc'  => 'Fun and gentle haircuts for the little ones.',
        'items' => [
          ['Kids Haircut (12 & below)', 'Fun, gentle haircut for kids 12 and below.', '₱250', '25 mins'],
        ],
      ],
      [
        'icon'  => 'fa-star',
        'name'  => 'BG Packages',
        'desc'  => 'The best value combos — everything your glow-up needs in one booking.',
        'items' => [
          ['BG Classic Package',  'Haircut + Hot Towel Shave.',                                    '₱750',  '55 mins'],
          ['BG Premium Package',  'Haircut + Hot Towel Shave + Beard Trim + Scalp Massage.',       '₱999',  '90 mins'],
          ['BG Glow-Up Package',  'Haircut + Hair Treatment + Blowdry.',                           '₱900',  '75 mins'],
        ],
      ],
    ];
    foreach ($categories as $ci => $cat): ?>

    <div class="svc-category fade-up <?= $ci % 2 !== 0 ? 'svc-cat-alt' : '' ?>">
      <div class="row align-items-start g-4">

        <!-- Category header -->
        <div class="col-lg-3">
          <div class="svc-cat-header">
            <div class="svc-cat-icon"><i class="fas <?= $cat['icon'] ?>"></i></div>
            <h3 class="svc-cat-name"><?= $cat['name'] ?></h3>
            <p class="svc-cat-desc"><?= $cat['desc'] ?></p>
            <a href="<?= BASE_PATH ?>views/user/booking.php" class="btn-gold mt-3">
              <i class="fas fa-calendar-check"></i> Book Now
            </a>
          </div>
        </div>

        <!-- Service items -->
        <div class="col-lg-9">
          <div class="svc-items">
            <?php foreach ($cat['items'] as $item): ?>
            <div class="svc-item">
              <div class="svc-item-left">
                <h5 class="svc-item-name"><?= $item[0] ?></h5>
                <p class="svc-item-desc"><?= $item[1] ?></p>
              </div>
              <div class="svc-item-right">
                <span class="svc-item-price"><?= $item[2] ?></span>
                <span class="svc-item-duration"><i class="far fa-clock"></i> <?= $item[3] ?></span>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

      </div>
    </div>

    <?php endforeach; ?>

  </div>
</section>

<!-- CTA STRIP -->
<section class="sec-pad-sm bg-black-sec">
  <div class="container">
    <div class="row align-items-center justify-content-between g-4">
      <div class="col-lg-7">
        <span class="sec-label">Ready?</span>
        <h2 class="sec-title white mt-2 mb-0">Book your slot <em style="color:var(--gold)">today.</em></h2>
      </div>
      <div class="col-lg-auto">
        <a href="<?= BASE_PATH ?>views/user/booking.php" class="btn-gold">
          <i class="fas fa-calendar-check"></i> Book Appointment
        </a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
