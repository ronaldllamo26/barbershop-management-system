<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
  <div class="container">
    <a class="navbar-brand" href="/bg-barbershop/index.php">
      <span class="brand-bg">BG</span>
      <span class="brand-name">Biglang Gwapo <em>Barbershop</em></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
        <?php
        $current = basename($_SERVER['PHP_SELF']);
        $isHome  = $current === 'index.php' && strpos($_SERVER['REQUEST_URI'], 'views') === false;
        $home    = '/bg-barbershop/index.php';

        // Nav items: [label, href]
        $navItems = [
          ['Home',       $isHome ? '#hero'    : $home . '#hero'],
          ['About',      '/bg-barbershop/views/user/about.php'],
          ['Services',   '/bg-barbershop/views/user/services.php'],
          ['Our Barbers',$isHome ? '#barbers' : $home . '#barbers'],
          ['Gallery',    '/bg-barbershop/views/user/gallery.php'],
          ['Contact',    '/bg-barbershop/views/user/contact.php'],
        ];

        foreach ($navItems as [$label, $href]):
          // Mark active page
          $pageFile = basename(parse_url($href, PHP_URL_PATH));
          $isActive = $current === $pageFile && $current !== 'index.php';
        ?>
        <li class="nav-item">
          <a class="nav-link <?= $isActive ? 'active-page' : '' ?>"
             href="<?= $href ?>"><?= $label ?></a>
        </li>
        <?php endforeach; ?>

        <!-- Account -->
        <?php if (!empty($_SESSION['customer_id'])): ?>
        <li class="nav-item ms-lg-1 dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"
             style="color:var(--gold) !important;">
            <i class="fas fa-user-circle me-1"></i>
            <?= htmlspecialchars(explode(' ', $_SESSION['customer_name'])[0]) ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" style="border:1px solid var(--light-2);border-radius:0;box-shadow:0 4px 20px rgba(0,0,0,.1);">
            <li><a class="dropdown-item" href="/bg-barbershop/views/user/my_bookings.php">
              <i class="fas fa-calendar-check me-2" style="color:var(--gold-d)"></i>My Bookings
            </a></li>
            <li><a class="dropdown-item" href="/bg-barbershop/views/user/profile.php">
              <i class="fas fa-user-edit me-2" style="color:var(--gold-d)"></i>Edit Profile
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="/bg-barbershop/views/user/logout.php">
              <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a></li>
          </ul>
        </li>
        <?php else: ?>
        <li class="nav-item ms-lg-1">
          <a class="nav-link" href="/bg-barbershop/views/user/login.php">
            <i class="fas fa-sign-in-alt me-1"></i>Login
          </a>
        </li>
        <?php endif; ?>

        <li class="nav-item ms-lg-1">
          <a class="btn-nav-book" href="/bg-barbershop/views/user/booking.php">
            <i class="fas fa-calendar-check me-2"></i>Book Now
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>