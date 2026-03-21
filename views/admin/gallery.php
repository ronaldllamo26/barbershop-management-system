<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/admin/auth.php';
requireAdmin();
$pageTitle = 'Gallery';

$alert = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $title    = $conn->real_escape_string(trim($_POST['title']));
        $category = $conn->real_escape_string($_POST['category']);
        $featured = isset($_POST['is_featured']) ? 1 : 0;
        $imgUrl   = $conn->real_escape_string(trim($_POST['image_url']));

        // Handle file upload
        if (!empty($_FILES['image_file']['name'])) {
            $uploadDir  = __DIR__ . '/../../assets/images/gallery/';
            $filename   = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['image_file']['name']);
            $targetPath = $uploadDir . $filename;
            $allowed    = ['jpg','jpeg','png','webp'];
            $ext        = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed) && move_uploaded_file($_FILES['image_file']['tmp_name'], $targetPath)) {
                $imgUrl = $conn->real_escape_string('/bg-barbershop/assets/images/gallery/' . $filename);
            }
        }

        if ($imgUrl) {
            $conn->query("INSERT INTO gallery (title, image_path, category, is_featured, is_active)
                          VALUES ('$title', '$imgUrl', '$category', $featured, 1)");
            $alert = 'success|Photo added to gallery!';
        } else {
            $alert = 'error|Please provide an image URL or upload a file.';
        }
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        // Get image path
        $res = $conn->query("SELECT image_path FROM gallery WHERE id=$id");
        if ($res && $row = $res->fetch_assoc()) {
            // Delete file if local
            $localPath = str_replace('/bg-barbershop/', __DIR__ . '/../../', $row['image_path']);
            if (file_exists($localPath) && strpos($row['image_path'], '/assets/') !== false) {
                @unlink($localPath);
            }
        }
        $conn->query("DELETE FROM gallery WHERE id=$id");
        $alert = 'success|Photo deleted.';
    }

    if ($action === 'toggle_featured') {
        $id  = intval($_POST['id']);
        $val = intval($_POST['value']);
        $conn->query("UPDATE gallery SET is_featured=$val WHERE id=$id");
        $alert = 'success|Updated.';
    }
}

$catFilter = $conn->real_escape_string($_GET['cat'] ?? 'all');
$where     = $catFilter !== 'all' ? "WHERE category='$catFilter'" : '';
$photos    = $conn->query("SELECT * FROM gallery $where ORDER BY uploaded_at DESC");
$totalAll  = $conn->query("SELECT COUNT(*) c FROM gallery")->fetch_assoc()['c'];

require_once __DIR__ . '/../../includes/admin/admin_header.php';

if ($alert) {
    [$type, $msg] = explode('|', $alert, 2);
    echo "<div class='admin-alert $type'><i class='fas fa-" . ($type==='success'?'check':'exclamation') . "-circle me-2'></i>$msg</div>";
}
?>

<div class="admin-section-header">
  <div class="admin-section-title">Gallery <span style="color:var(--gray);font-size:.85rem;font-family:var(--font-b);font-weight:400;">(<?= $totalAll ?> photos)</span></div>
  <button class="btn-admin-gold" onclick="openModal('addPhotoModal')">
    <i class="fas fa-plus me-1"></i> Add Photo
  </button>
</div>

<!-- Category Filter -->
<div class="d-flex gap-2 flex-wrap mb-4">
  <?php
  $cats = ['all'=>'All', 'haircut'=>'Haircut', 'beard'=>'Beard', 'shave'=>'Shave', 'shop'=>'The Shop', 'team'=>'Team', 'before_after'=>'Before & After'];
  foreach ($cats as $val => $lbl):
    $active = $catFilter === $val ? 'btn-admin-dark' : 'btn-admin-outline';
  ?>
  <a href="?cat=<?= $val ?>" class="<?= $active ?>" style="padding:7px 16px;font-size:.68rem;">
    <?= $lbl ?>
  </a>
  <?php endforeach; ?>
</div>

<!-- Gallery Grid -->
<div class="row g-3">
  <?php if ($photos && $photos->num_rows > 0):
    while ($p = $photos->fetch_assoc()): ?>
  <div class="col-6 col-md-4 col-lg-3">
    <div class="admin-gallery-card">
      <div class="agc-img-wrap">
        <img src="<?= htmlspecialchars($p['image_path']) ?>" alt="<?= htmlspecialchars($p['title']) ?>"
             onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
        <?php if ($p['is_featured']): ?>
        <span class="agc-featured-badge"><i class="fas fa-star"></i> Featured</span>
        <?php endif; ?>
      </div>
      <div class="agc-info">
        <span class="agc-title"><?= htmlspecialchars($p['title'] ?: 'Untitled') ?></span>
        <span class="agc-cat"><?= ucfirst(str_replace('_',' ',$p['category'])) ?></span>
        <div class="agc-actions">
          <form method="POST" style="display:inline;">
            <input type="hidden" name="action" value="toggle_featured">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <input type="hidden" name="value" value="<?= $p['is_featured'] ? 0 : 1 ?>">
            <button type="submit" class="btn-sm-action" title="<?= $p['is_featured'] ? 'Unfeature' : 'Set as Featured' ?>">
              <i class="fas fa-star" style="color:<?= $p['is_featured'] ? 'var(--gold)' : 'var(--gray)' ?>"></i>
            </button>
          </form>
          <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this photo?')">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <button type="submit" class="btn-sm-action cancel">
              <i class="fas fa-trash"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endwhile; else: ?>
  <div class="col-12">
    <div style="text-align:center;padding:48px;color:var(--gray);background:var(--white);border:1px solid var(--light-2);">
      <i class="fas fa-images" style="font-size:2rem;margin-bottom:12px;display:block;"></i>
      No photos yet. Add your first photo!
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- Add Photo Modal -->
<div class="admin-modal-backdrop" id="addPhotoModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h5>Add Photo to Gallery</h5>
      <button class="modal-close-btn" onclick="closeModal('addPhotoModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="add">
      <div class="admin-modal-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="admin-label">Title (optional)</label>
            <input type="text" name="title" class="admin-input" placeholder="e.g. Skin Fade">
          </div>
          <div class="col-12">
            <label class="admin-label">Category *</label>
            <select name="category" class="admin-input" required>
              <option value="haircut">Haircut</option>
              <option value="beard">Beard</option>
              <option value="shave">Shave</option>
              <option value="shop">The Shop</option>
              <option value="team">Team</option>
              <option value="before_after">Before & After</option>
            </select>
          </div>
          <div class="col-12">
            <label class="admin-label">Upload Photo</label>
            <input type="file" name="image_file" class="admin-input" accept="image/*"
                   onchange="previewUpload(this)">
            <img id="uploadPreview" src="" style="display:none;margin-top:8px;width:100%;max-height:160px;object-fit:cover;">
          </div>
          <div class="col-12">
            <label class="admin-label">Or paste Image URL</label>
            <input type="text" name="image_url" class="admin-input" placeholder="https://images.unsplash.com/...">
          </div>
          <div class="col-12">
            <label class="admin-label" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
              <input type="checkbox" name="is_featured" value="1" style="width:16px;height:16px;">
              Set as Featured Photo
            </label>
          </div>
        </div>
      </div>
      <div class="admin-modal-footer">
        <button type="button" class="btn-admin-outline" onclick="closeModal('addPhotoModal')">Cancel</button>
        <button type="submit" class="btn-admin-gold"><i class="fas fa-upload me-1"></i> Upload Photo</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id)  { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
document.querySelectorAll('.admin-modal-backdrop').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('active'); });
});

function previewUpload(input) {
  const prev = document.getElementById('uploadPreview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => { prev.src = e.target.result; prev.style.display = 'block'; };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php require_once __DIR__ . '/../../includes/admin/admin_footer.php'; ?>