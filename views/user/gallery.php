<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
$pageTitle = 'Gallery';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

$photos = [
  ['https://images.unsplash.com/photo-1605497788044-5a32c7078486?w=900&q=85&fit=crop', 'haircut',    'Skin Fade'],
  ['https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=900&q=85&fit=crop', 'haircut',    'Classic Cut'],
  ['https://images.unsplash.com/photo-1621605815971-fbc98d665033?w=900&q=85&fit=crop', 'haircut',    'Modern Style'],
  ['https://images.unsplash.com/photo-1599351431202-1e0f0137899a?w=900&q=85&fit=crop', 'team',       'Head Barber'],
  ['https://images.unsplash.com/photo-1622286342621-4bd786c2447c?w=900&q=85&fit=crop', 'team',       'Senior Barber'],
  ['https://images.unsplash.com/photo-1560869713-7d0a29430803?w=900&q=85&fit=crop', 'shave',      'Hot Towel Shave'],
  ['https://images.unsplash.com/photo-1519345182560-3f2917c472ef?w=900&q=85&fit=crop', 'beard',      'Beard Sculpt'],
  ['https://images.unsplash.com/photo-1503443207922-dff7d543fd0e?w=900&q=85&fit=crop', 'team',       'Fade Specialist'],
  ['https://images.unsplash.com/photo-1585747860715-2ba37e788b70?w=900&q=85&fit=crop', 'shop',       'BG Interior'],
  ['https://images.unsplash.com/photo-1534297635766-a262cdcb8ee4?w=900&q=85&fit=crop', 'haircut',    'Textured Crop'],
  ['https://images.unsplash.com/photo-1596728325488-58c87691e9af?w=900&q=85&fit=crop', 'beard',      'Beard Trim'],
  ['https://images.unsplash.com/photo-1473679408190-0693dd26c96f?w=900&q=85&fit=crop', 'shop',       'Shop Vibes'],
];
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