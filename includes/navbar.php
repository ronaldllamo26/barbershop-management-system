<nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
  <div class="container">
    <a class="navbar-brand" href="<?= BASE_PATH ?>index.php">
      <span class="brand-bg">BG</span>
      <span class="brand-name">Biglang Gwapo <em>Barbershop</em></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
        <?php
        $isHome  = basename($_SERVER['PHP_SELF']) === 'index.php'
                   && strpos($_SERVER['REQUEST_URI'], 'views') === false;
        $home    = BASE_PATH . 'index.php';
        $current = basename($_SERVER['PHP_SELF']);

        // Anchor links (scroll on homepage, redirect from other pages)
        $anchors = [
          ['hero',     'Home'],
          ['about',    'About'],
          ['services', 'Services'],
          ['barbers',  'Our Barbers'],
          ['gallery',  'Gallery'],
          ['contact',  'Contact'],
        ];
        foreach ($anchors as [$anchor, $label]):
          $href = $isHome ? "#$anchor" : "{$home}#{$anchor}";
        ?>
        <li class="nav-item">
          <a class="nav-link <?= $isHome ? 'anchor-link' : '' ?>" href="<?= $href ?>"><?= $label ?></a>
        </li>
        <?php endforeach; ?>
        <li class="nav-item ms-lg-3">
          <a class="btn-nav-book" href="<?= BASE_PATH ?>views/user/booking.php">
            <i class="fas fa-calendar-check me-2"></i>Book Now
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>