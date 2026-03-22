<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
$pageTitle = 'Gallery';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

// Load from DB
$photosRes = $conn->query("SELECT * FROM gallery WHERE is_active=1 ORDER BY uploaded_at DESC");
$photos = [];
while ($row = $photosRes->fetch_assoc()) {
    $photos[] = [$row['image_path'], $row['category'], $row['title'] ?: 'BG Barbershop'];
}
?>

<!-- PAGE HERO -->
<section class="page-hero">
  <div class="page-hero-overlay"></div>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center page-hero-content">
        <span class="sec-label">The Work</span>
        <div class="divider center"></div>
        <h1 class="page-hero-title">Our <em style="color:var(--gold)">Gallery</em></h1>
        <p class="page-hero-sub">Every cut tells a story — here's ours</p>
      </div>
    </div>
  </div>
</section>

<!-- FILTER TABS -->
<section class="sec-pad-sm bg-light-sec">
  <div class="container">

    <div class="gallery-filters mb-5 fade-up">
      <button class="gal-filter active" data-filter="all">All</button>
      <button class="gal-filter" data-filter="haircut">Haircut</button>
      <button class="gal-filter" data-filter="beard">Beard</button>
      <button class="gal-filter" data-filter="shave">Shave</button>
      <button class="gal-filter" data-filter="team">Our Team</button>
      <button class="gal-filter" data-filter="shop">The Shop</button>
    </div>

    <div class="gallery-masonry fade-up" id="galleryGrid">
      <?php foreach ($photos as $i => [$url, $cat, $label]): ?>
      <div class="gal-masonry-item" data-category="<?= $cat ?>">
        <div class="gal-masonry-inner">
          <img src="<?= $url ?>" alt="<?= $label ?>" loading="lazy">
          <div class="gal-masonry-overlay">
            <div class="gal-masonry-info">
              <span class="gal-masonry-label"><?= $label ?></span>
              <span class="gal-masonry-cat"><?= ucfirst($cat) ?></span>
            </div>
            <button class="gal-zoom-btn" onclick="openLightbox(<?= $i ?>)">
              <i class="fas fa-expand-alt"></i>
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div id="noResults" class="text-center py-5" style="display:none;">
      <p style="color:var(--gray);font-size:1rem;">No photos in this category yet.</p>
    </div>

  </div>
</section>

<!-- LIGHTBOX -->
<div class="lightbox-overlay" id="lightbox" onclick="closeLightbox()">
  <button class="lb-close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
  <button class="lb-prev" onclick="lbNav(-1,event)"><i class="fas fa-chevron-left"></i></button>
  <button class="lb-next" onclick="lbNav(1,event)"><i class="fas fa-chevron-right"></i></button>
  <div class="lb-content" onclick="event.stopPropagation()">
    <img id="lbImg" src="" alt="">
    <div class="lb-caption">
      <span id="lbLabel"></span>
      <span id="lbCounter"></span>
    </div>
  </div>
</div>

<!-- CTA -->
<section class="sec-pad-sm bg-black-sec">
  <div class="container">
    <div class="row align-items-center justify-content-between g-4">
      <div class="col-lg-7">
        <span class="sec-label">Like What You See?</span>
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

<script>
const allPhotos = <?= json_encode($photos) ?>;
let currentIndex = 0;
let visibleIndices = [];

// ── Filter ──
document.querySelectorAll('.gal-filter').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.gal-filter').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    const filter = this.dataset.filter;
    let count = 0;
    document.querySelectorAll('.gal-masonry-item').forEach(item => {
      const show = filter === 'all' || item.dataset.category === filter;
      item.style.display = show ? '' : 'none';
      if (show) count++;
    });
    document.getElementById('noResults').style.display = count === 0 ? 'block' : 'none';
    updateVisibleIndices();
  });
});

function updateVisibleIndices() {
  visibleIndices = [];
  document.querySelectorAll('.gal-masonry-item').forEach((item, i) => {
    if (item.style.display !== 'none') visibleIndices.push(i);
  });
}
updateVisibleIndices();

// ── Lightbox ──
function openLightbox(index) {
  currentIndex = index;
  const [url, cat, label] = allPhotos[index];
  document.getElementById('lbImg').src = url;
  document.getElementById('lbLabel').textContent = label;
  const pos = visibleIndices.indexOf(index);
  document.getElementById('lbCounter').textContent = `${pos + 1} / ${visibleIndices.length}`;
  document.getElementById('lightbox').classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeLightbox() {
  document.getElementById('lightbox').classList.remove('active');
  document.body.style.overflow = '';
}

function lbNav(dir, e) {
  e.stopPropagation();
  const pos = visibleIndices.indexOf(currentIndex);
  const next = (pos + dir + visibleIndices.length) % visibleIndices.length;
  openLightbox(visibleIndices[next]);
}

// Keyboard navigation
document.addEventListener('keydown', e => {
  if (!document.getElementById('lightbox').classList.contains('active')) return;
  if (e.key === 'ArrowRight') lbNav(1, { stopPropagation: () => {} });
  if (e.key === 'ArrowLeft')  lbNav(-1, { stopPropagation: () => {} });
  if (e.key === 'Escape')     closeLightbox();
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>