<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= isset($pageTitle) ? $pageTitle . ' | BG Admin' : 'BG Admin Panel' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/bg-barbershop/assets/css/admin.css">
</head>
<body class="admin-body">

<?php $admin = currentAdmin(); $current = basename($_SERVER['PHP_SELF']); ?>

<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-brand">
    <span class="sb-logo">BG</span>
    <div class="sb-text">
      <span class="sb-title">Admin Panel</span>
      <span class="sb-sub">Biglang Gwapo</span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-group-label">Main</div>
    <?php
    $navItems = [
      ['dashboard.php',    'fa-tachometer-alt', 'Dashboard'],
      ['appointments.php', 'fa-calendar-check', 'Appointments'],
      ['services.php',     'fa-cut',            'Services'],
      ['barbers.php',      'fa-user-tie',       'Barbers'],
      ['gallery.php',      'fa-images',         'Gallery'],
      ['settings.php',     'fa-cog',            'Settings'],
    ];
    foreach ($navItems as [$file, $icon, $label]):
      $active = $current === $file ? 'active' : '';
    ?>
    <a class="sidebar-link <?= $active ?>" href="/bg-barbershop/views/admin/<?= $file ?>">
      <i class="fas <?= $icon ?>"></i>
      <span><?= $label ?></span>
    </a>
    <?php endforeach; ?>

    <div class="nav-group-label mt-3">Account</div>
    <a class="sidebar-link" href="/bg-barbershop/index.php" target="_blank">
      <i class="fas fa-external-link-alt"></i>
      <span>View Site</span>
    </a>
    <a class="sidebar-link text-danger-link" href="/bg-barbershop/views/admin/logout.php">
      <i class="fas fa-sign-out-alt"></i>
      <span>Logout</span>
    </a>
  </nav>

  <div class="sidebar-user">
    <div class="su-avatar"><i class="fas fa-user-shield"></i></div>
    <div class="su-info">
      <span class="su-name"><?= htmlspecialchars($admin['name']) ?></span>
      <span class="su-role"><?= ucfirst($admin['role']) ?></span>
    </div>
  </div>
</aside>

<!-- Main wrapper -->
<div class="admin-main" id="adminMain">
  <!-- Top bar -->
  <header class="admin-topbar">
    <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
      <i class="fas fa-bars"></i>
    </button>
    <div class="topbar-title"><?= $pageTitle ?? 'Dashboard' ?></div>
    <div class="topbar-right">
      <span class="topbar-date"><?= date('D, M j Y') ?></span>
      <a href="/bg-barbershop/views/admin/logout.php" class="topbar-logout" title="Logout">
        <i class="fas fa-sign-out-alt"></i>
      </a>
    </div>
  </header>

  <!-- Page content starts here -->
  <div class="admin-content"></div>