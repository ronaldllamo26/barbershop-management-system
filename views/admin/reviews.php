<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/admin/auth.php';
requireAdmin();
$pageTitle = 'Reviews';

$alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $id       = intval($_POST['id'] ?? 0);
        $name     = $conn->real_escape_string(trim($_POST['customer_name']));
        $location = $conn->real_escape_string(trim($_POST['location']));
        $text     = $conn->real_escape_string(trim($_POST['review_text']));
        $rating   = intval($_POST['rating']);
        $featured = isset($_POST['is_featured']) ? 1 : 0;
        $active   = isset($_POST['is_active']) ? 1 : 0;

        if ($action === 'add') {
            $conn->query("INSERT INTO testimonials (customer_name,location,review_text,rating,is_featured,is_active)
                          VALUES ('$name','$location','$text',$rating,$featured,$active)");
            $alert = 'success|Review added!';
        } else {
            $conn->query("UPDATE testimonials SET customer_name='$name', location='$location',
                          review_text='$text', rating=$rating, is_featured=$featured, is_active=$active
                          WHERE id=$id");
            $alert = 'success|Review updated!';
        }
    }

    if ($action === 'delete') {
        $conn->query("DELETE FROM testimonials WHERE id=" . intval($_POST['id']));
        $alert = 'success|Review deleted.';
    }

    if ($action === 'toggle') {
        $id  = intval($_POST['id']);
        $col = $_POST['col'] === 'featured' ? 'is_featured' : 'is_active';
        $val = intval($_POST['value']);
        $conn->query("UPDATE testimonials SET $col=$val WHERE id=$id");
        $alert = 'success|Updated.';
    }
}

$reviews = $conn->query("SELECT * FROM testimonials ORDER BY is_featured DESC, created_at DESC");
require_once __DIR__ . '/../../includes/admin/admin_header.php';

if ($alert) {
    [$type, $msg] = explode('|', $alert, 2);
    echo "<div class='admin-alert $type'><i class='fas fa-check-circle me-2'></i>$msg</div>";
}
?>

<div class="admin-section-header">
  <div class="admin-section-title">Customer Reviews</div>
  <button class="btn-admin-gold" onclick="openModal('addReviewModal')">
    <i class="fas fa-plus me-1"></i> Add Review
  </button>
</div>

<!-- Reviews Grid -->
<div class="row g-3">
  <?php while ($r = $reviews->fetch_assoc()): ?>
  <div class="col-md-6 col-lg-4">
    <div class="review-admin-card <?= !$r['is_active'] ? 'inactive' : '' ?>">

      <!-- Header -->
      <div class="rac-header">
        <div class="rac-stars">
          <?php for ($i = 1; $i <= 5; $i++): ?>
          <svg width="14" height="14" viewBox="0 0 24 24"
               fill="<?= $i <= $r['rating'] ? '#C9A84C' : 'none' ?>"
               stroke="#C9A84C" stroke-width="2">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
          <?php endfor; ?>
        </div>
        <div class="rac-badges">
          <?php if ($r['is_featured']): ?>
          <span class="rac-badge featured">Featured</span>
          <?php endif; ?>
          <?php if (!$r['is_active']): ?>
          <span class="rac-badge inactive">Hidden</span>
          <?php endif; ?>
        </div>
      </div>

      <!-- Review Text -->
      <p class="rac-text">"<?= htmlspecialchars($r['review_text']) ?>"</p>

      <!-- Reviewer -->
      <div class="rac-reviewer">
        <div class="rac-avatar">
          <?= strtoupper(substr($r['customer_name'], 0, 1)) ?>
        </div>
        <div>
          <strong><?= htmlspecialchars($r['customer_name']) ?></strong>
          <span><?= htmlspecialchars($r['location'] ?: '—') ?></span>
        </div>
      </div>

      <!-- Actions -->
      <div class="rac-actions">
        <button class="btn-sm-action confirm" onclick='editReview(<?= json_encode($r) ?>)'>
          <i class="fas fa-edit me-1"></i>Edit
        </button>

        <!-- Toggle Featured -->
        <form method="POST" style="display:inline;">
          <input type="hidden" name="action" value="toggle">
          <input type="hidden" name="id" value="<?= $r['id'] ?>">
          <input type="hidden" name="col" value="featured">
          <input type="hidden" name="value" value="<?= $r['is_featured'] ? 0 : 1 ?>">
          <button type="submit" class="btn-sm-action" title="<?= $r['is_featured'] ? 'Unfeature' : 'Set Featured' ?>">
            <svg width="12" height="12" viewBox="0 0 24 24"
                 fill="<?= $r['is_featured'] ? '#C9A84C' : 'none' ?>"
                 stroke="<?= $r['is_featured'] ? '#C9A84C' : 'currentColor' ?>" stroke-width="2">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
          </button>
        </form>

        <!-- Toggle Active -->
        <form method="POST" style="display:inline;">
          <input type="hidden" name="action" value="toggle">
          <input type="hidden" name="id" value="<?= $r['id'] ?>">
          <input type="hidden" name="col" value="active">
          <input type="hidden" name="value" value="<?= $r['is_active'] ? 0 : 1 ?>">
          <button type="submit" class="btn-sm-action <?= $r['is_active'] ? '' : 'confirm' ?>">
            <?= $r['is_active'] ? 'Hide' : 'Show' ?>
          </button>
        </form>

        <!-- Delete -->
        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this review?')">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= $r['id'] ?>">
          <button type="submit" class="btn-sm-action cancel">
            <i class="fas fa-trash"></i>
          </button>
        </form>
      </div>

    </div>
  </div>
  <?php endwhile; ?>
</div>

<!-- Add Modal -->
<div class="admin-modal-backdrop" id="addReviewModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h5>Add Review</h5>
      <button class="modal-close-btn" onclick="closeModal('addReviewModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="add">
      <div class="admin-modal-body">
        <?php include __DIR__ . '/../../includes/admin/review_form_fields.php'; ?>
      </div>
      <div class="admin-modal-footer">
        <button type="button" class="btn-admin-outline" onclick="closeModal('addReviewModal')">Cancel</button>
        <button type="submit" class="btn-admin-gold"><i class="fas fa-save me-1"></i> Save Review</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="admin-modal-backdrop" id="editReviewModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h5>Edit Review</h5>
      <button class="modal-close-btn" onclick="closeModal('editReviewModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit_review_id">
      <div class="admin-modal-body">
        <?php include __DIR__ . '/../../includes/admin/review_form_fields.php'; ?>
      </div>
      <div class="admin-modal-footer">
        <button type="button" class="btn-admin-outline" onclick="closeModal('editReviewModal')">Cancel</button>
        <button type="submit" class="btn-admin-gold"><i class="fas fa-save me-1"></i> Update Review</button>
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

function editReview(r) {
  const m = document.getElementById('editReviewModal');
  m.querySelector('#edit_review_id').value          = r.id;
  m.querySelector('[name="customer_name"]').value    = r.customer_name;
  m.querySelector('[name="location"]').value         = r.location || '';
  m.querySelector('[name="review_text"]').value      = r.review_text;
  m.querySelector('[name="rating"]').value           = r.rating;
  m.querySelector('[name="is_featured"]').checked    = r.is_featured == 1;
  m.querySelector('[name="is_active"]').checked      = r.is_active == 1;
  // Update star display
  updateStars(m.querySelector('[name="rating"]'));
  openModal('editReviewModal');
}

function updateStars(select) {
  const val    = parseInt(select.value);
  const modal  = select.closest('.admin-modal-body');
  const stars  = modal.querySelectorAll('.star-btn');
  stars.forEach((s, i) => {
    s.style.color = i < val ? '#C9A84C' : '#ccc';
  });
}
</script>

<?php require_once __DIR__ . '/../../includes/admin/admin_footer.php'; ?>