<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/admin/auth.php';
requireAdmin();
$pageTitle = 'Barbers';

$alert = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $id         = intval($_POST['id'] ?? 0);
        $fname      = $conn->real_escape_string(trim($_POST['first_name']));
        $lname      = $conn->real_escape_string(trim($_POST['last_name']));
        $role       = $conn->real_escape_string(trim($_POST['role_title']));
        $bio        = $conn->real_escape_string(trim($_POST['bio']));
        $instagram  = $conn->real_escape_string(trim($_POST['instagram']));
        $facebook   = $conn->real_escape_string(trim($_POST['facebook']));
        $active     = isset($_POST['is_active']) ? 1 : 0;
        $sort       = intval($_POST['sort_order'] ?? 0);

        // Handle photo URL
        $photo = $conn->real_escape_string(trim($_POST['photo'] ?? ''));

        if ($action === 'add') {
            $conn->query("INSERT INTO barbers (first_name,last_name,role_title,bio,photo,instagram,facebook,is_active,sort_order)
                          VALUES ('$fname','$lname','$role','$bio','$photo','$instagram','$facebook',$active,$sort)");
            $alert = 'success|Barber added successfully!';
        } else {
            $conn->query("UPDATE barbers SET first_name='$fname', last_name='$lname', role_title='$role',
                          bio='$bio', photo='$photo', instagram='$instagram', facebook='$facebook',
                          is_active=$active, sort_order=$sort WHERE id=$id");
            $alert = 'success|Barber updated!';
        }
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM barbers WHERE id=$id");
        $alert = 'success|Barber removed.';
    }

    if ($action === 'toggle_active') {
        $id  = intval($_POST['id']);
        $val = intval($_POST['value']);
        $conn->query("UPDATE barbers SET is_active=$val WHERE id=$id");
        $alert = 'success|Status updated.';
    }
}

$barbers = $conn->query("SELECT * FROM barbers ORDER BY sort_order, id");
require_once __DIR__ . '/../../includes/admin/admin_header.php';

if ($alert) {
    [$type, $msg] = explode('|', $alert, 2);
    echo "<div class='admin-alert $type'><i class='fas fa-check-circle me-2'></i>$msg</div>";
}
?>

<div class="admin-section-header">
  <div class="admin-section-title">Barber Profiles</div>
  <button class="btn-admin-gold" onclick="openModal('addBarberModal')">
    <i class="fas fa-plus me-1"></i> Add Barber
  </button>
</div>

<!-- Barber Cards -->
<div class="row g-4">
  <?php while ($b = $barbers->fetch_assoc()): ?>
  <div class="col-md-6 col-lg-3">
    <div class="admin-barber-card">
      <div class="abc-img-wrap">
        <?php if ($b['photo']): ?>
        <img src="<?= htmlspecialchars($b['photo']) ?>" alt="<?= $b['first_name'] ?>">
        <?php else: ?>
        <div class="abc-img-placeholder"><i class="fas fa-user-tie"></i></div>
        <?php endif; ?>
        <span class="abc-status <?= $b['is_active'] ? 'active' : 'inactive' ?>">
          <?= $b['is_active'] ? 'Active' : 'Inactive' ?>
        </span>
      </div>
      <div class="abc-info">
        <h5><?= htmlspecialchars($b['first_name'] . ' ' . $b['last_name']) ?></h5>
        <span class="abc-role"><?= htmlspecialchars($b['role_title']) ?></span>
        <?php if ($b['bio']): ?>
        <p class="abc-bio"><?= htmlspecialchars($b['bio']) ?></p>
        <?php endif; ?>
        <div class="abc-actions">
          <button class="btn-sm-action confirm" onclick='editBarber(<?= json_encode($b) ?>)'>
            <i class="fas fa-edit me-1"></i>Edit
          </button>
          <form method="POST" style="display:inline;" onsubmit="return confirm('Remove this barber?')">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $b['id'] ?>">
            <button type="submit" class="btn-sm-action cancel">
              <i class="fas fa-trash me-1"></i>Delete
            </button>
          </form>
          <form method="POST" style="display:inline;">
            <input type="hidden" name="action" value="toggle_active">
            <input type="hidden" name="id" value="<?= $b['id'] ?>">
            <input type="hidden" name="value" value="<?= $b['is_active'] ? 0 : 1 ?>">
            <button type="submit" class="btn-sm-action">
              <?= $b['is_active'] ? 'Deactivate' : 'Activate' ?>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endwhile; ?>
</div>

<!-- Add Modal -->
<div class="admin-modal-backdrop" id="addBarberModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h5>Add New Barber</h5>
      <button class="modal-close-btn" onclick="closeModal('addBarberModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="add">
      <div class="admin-modal-body">
        <?php include __DIR__ . '/../../includes/admin/barber_form_fields.php'; ?>
      </div>
      <div class="admin-modal-footer">
        <button type="button" class="btn-admin-outline" onclick="closeModal('addBarberModal')">Cancel</button>
        <button type="submit" class="btn-admin-gold"><i class="fas fa-save me-1"></i> Save Barber</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="admin-modal-backdrop" id="editBarberModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h5>Edit Barber</h5>
      <button class="modal-close-btn" onclick="closeModal('editBarberModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit_barber_id">
      <div class="admin-modal-body">
        <?php include __DIR__ . '/../../includes/admin/barber_form_fields.php'; ?>
      </div>
      <div class="admin-modal-footer">
        <button type="button" class="btn-admin-outline" onclick="closeModal('editBarberModal')">Cancel</button>
        <button type="submit" class="btn-admin-gold"><i class="fas fa-save me-1"></i> Update Barber</button>
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

function editBarber(b) {
  const m = document.getElementById('editBarberModal');
  m.querySelector('#edit_barber_id').value           = b.id;
  m.querySelector('[name="first_name"]').value        = b.first_name;
  m.querySelector('[name="last_name"]').value         = b.last_name;
  m.querySelector('[name="role_title"]').value        = b.role_title;
  m.querySelector('[name="bio"]').value               = b.bio || '';
  m.querySelector('[name="photo"]').value             = b.photo || '';
  m.querySelector('[name="instagram"]').value         = b.instagram || '';
  m.querySelector('[name="facebook"]').value          = b.facebook || '';
  m.querySelector('[name="sort_order"]').value        = b.sort_order;
  m.querySelector('[name="is_active"]').checked       = b.is_active == 1;
  // Update preview
  const prev = m.querySelector('#photoPreview');
  if (prev) prev.src = b.photo || '';
  openModal('editBarberModal');
}
</script>

<?php require_once __DIR__ . '/../../includes/admin/admin_footer.php'; ?>