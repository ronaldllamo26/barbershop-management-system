<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
$pageTitle = 'Services';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

// Load categories with their services from DB
$catIcons = [
    'Haircut & Style'      => 'fa-cut',
    'Shave'                => 'fa-fire',
    'Beard'                => 'fa-user-tie',
    'Treatment'            => 'fa-spa',
    'Kids'                 => 'fa-child',
    'Packages'             => 'fa-star',
];

$catsRes = $conn->query(
    "SELECT sc.*, GROUP_CONCAT(
        s.id,'||',s.name,'||',COALESCE(s.description,''),'||',s.price,'||',s.duration_mins
        ORDER BY s.sort_order, s.id
        SEPARATOR ';;'
     ) AS services_data
     FROM service_categories sc
     LEFT JOIN services s ON s.category_id = sc.id AND s.is_active = 1
     GROUP BY sc.id
     ORDER BY sc.sort_order"
);
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
    $ci = 0;
    while ($cat = $catsRes->fetch_assoc()):
      if (empty($cat['services_data'])) { continue; }
      $icon     = $catIcons[$cat['name']] ?? 'fa-scissors';
      $services = explode(';;', $cat['services_data']);
    ?>

    <div class="svc-category fade-up <?= $ci % 2 !== 0 ? 'svc-cat-alt' : '' ?>">
      <div class="row align-items-start g-4">

        <!-- Category header -->
        <div class="col-lg-3">
          <div class="svc-cat-header">
            <div class="svc-cat-icon"><i class="fas <?= $icon ?>"></i></div>
            <h3 class="svc-cat-name"><?= htmlspecialchars($cat['name']) ?></h3>
            <p class="svc-cat-desc"><?= htmlspecialchars($cat['description'] ?? '') ?></p>
            <a href="<?= BASE_PATH ?>views/user/booking.php" class="btn-gold mt-3">
              <i class="fas fa-calendar-check"></i> Book Now
            </a>
          </div>
        </div>

        <!-- Service items -->
        <div class="col-lg-9">
          <div class="svc-items">
            <?php foreach ($services as $svcRaw):
              $parts = explode('||', $svcRaw);
              if (count($parts) < 5) continue;
              [,$svcName, $svcDesc, $svcPrice, $svcDur] = $parts;
            ?>
            <div class="svc-item">
              <div class="svc-item-left">
                <h5 class="svc-item-name"><?= htmlspecialchars($svcName) ?></h5>
                <?php if ($svcDesc): ?>
                <p class="svc-item-desc"><?= htmlspecialchars($svcDesc) ?></p>
                <?php endif; ?>
              </div>
              <div class="svc-item-right">
                <span class="svc-item-price">₱<?= number_format($svcPrice) ?></span>
                <span class="svc-item-duration">
                  <i class="far fa-clock"></i> <?= $svcDur ?> mins
                </span>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

      </div>
    </div>

    <?php $ci++; endwhile; ?>

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