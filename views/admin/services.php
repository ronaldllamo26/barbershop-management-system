<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/admin/auth.php';
requireAdmin();
$pageTitle = 'Services';

// Handle actions
$alert = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $id       = intval($_POST['id'] ?? 0);
        $cat_id   = intval($_POST['category_id']);
        $name     = $conn->real_escape_string(trim($_POST['name']));
        $desc     = $conn->real_escape_string(trim($_POST['description']));
        $price    = floatval($_POST['price']);
        $duration = intval($_POST['duration_mins']);
        $featured = isset($_POST['is_featured']) ? 1 : 0;
        $active   = isset($_POST['is_active']) ? 1 : 0;

        if ($action === 'add') {
            $conn->query("INSERT INTO services (category_id,name,description,price,duration_mins,is_featured,is_active)
                          VALUES ($cat_id,'$name','$desc',$price,$duration,$featured,$active)");
            $alert = 'success|Service added successfully!';
        } else {
            $conn->query("UPDATE services SET category_id=$cat_id, name='$name', description='$desc',
                          price=$price, duration_mins=$duration, is_featured=$featured, is_active=$active
                          WHERE id=$id");
            $alert = 'success|Service updated successfully!';
        }
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM services WHERE id=$id");
        $alert = 'success|Service deleted.';
    }

    if ($action === 'toggle_active') {
        $id  = intval($_POST['id']);
        $val = intval($_POST['value']);
        $conn->query("UPDATE services SET is_active=$val WHERE id=$id");
        $alert = 'success|Status updated.';
    }
}

$categories = $conn->query("SELECT * FROM service_categories ORDER BY sort_order");
$services   = $conn->query(
    "SELECT s.*, sc.name AS cat_name FROM services s
     LEFT JOIN service_categories sc ON sc.id = s.category_id
     ORDER BY sc.sort_order, s.sort_order, s.id"
);

require_once __DIR__ . '/../../includes/admin/admin_header.php';

if ($alert) {
    [$type, $msg] = explode('|', $alert, 2);
    echo "<div class='admin-alert $type'><i class='fas fa-check-circle me-2'></i>$msg</div>";
}
?>

<div class="admin-section-header">
  <div class="admin-section-title">All Services</div>
  <button class="btn-admin-gold" onclick="openModal('addModal')">
    <i class="fas fa-plus me-1"></i> Add Service
  </button>
</div>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Service Name</th>
        <th>Category</th>
        <th>Price</th>
        <th>Duration</th>
        <th>Featured</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $services->fetch_assoc()): ?>
      <tr>
        <td>
          <strong><?= htmlspecialchars($row['name']) ?></strong><br>
          <span style="font-size:.75rem;color:var(--gray)"><?= htmlspecialchars($row['description']) ?></span>
        </td>
        <td><?= htmlspecialchars($row['cat_name']) ?></td>
        <td><strong>₱<?= number_format($row['price'], 2) ?></strong></td>
        <td><?= $row['duration_mins'] ?> mins</td>
        <td>
          <?php if ($row['is_featured']): ?>
          <span class="badge-status badge-confirmed">Yes</span>
          <?php else: ?>
          <span style="color:var(--gray);font-size:.8rem;">No</span>
          <?php endif; ?>
        </td>
        <td>
          <form method="POST" style="display:inline;">
            <input type="hidden" name="action" value="toggle_active">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input type="hidden" name="value" value="<?= $row['is_active'] ? 0 : 1 ?>">
            <button type="submit" class="badge-status <?= $row['is_active'] ? 'badge-confirmed' : 'badge-cancelled' ?>"
                    style="border:none;cursor:pointer;">
              <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
            </button>
          </form>
        </td>
        <td>
          <div class="d-flex gap-1">
            <button class="btn-sm-action confirm" onclick='editService(<?= json_encode($row) ?>)'>Edit</button>
            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this service?')">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <button type="submit" class="btn-sm-action cancel">Delete</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Add Modal -->
<div class="admin-modal-backdrop" id="addModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h5>Add New Service</h5>
      <button class="modal-close-btn" onclick="closeModal('addModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="add">
      <div class="admin-modal-body">
        <?php include __DIR__ . '/../../includes/admin/service_form_fields.php'; ?>
      </div>
      <div class="admin-modal-footer">
        <button type="button" class="btn-admin-outline" onclick="closeModal('addModal')">Cancel</button>
        <button type="submit" class="btn-admin-gold"><i class="fas fa-save me-1"></i> Save Service</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="admin-modal-backdrop" id="editModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h5>Edit Service</h5>
      <button class="modal-close-btn" onclick="closeModal('editModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit_id">
      <div class="admin-modal-body">
        <?php include __DIR__ . '/../../includes/admin/service_form_fields.php'; ?>
      </div>
      <div class="admin-modal-footer">
        <button type="button" class="btn-admin-outline" onclick="closeModal('editModal')">Cancel</button>
        <button type="submit" class="btn-admin-gold"><i class="fas fa-save me-1"></i> Update Service</button>
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

function editService(row) {
  const m = document.getElementById('editModal');
  m.querySelector('#edit_id').value                          = row.id;
  m.querySelector('[name="category_id"]').value              = row.category_id;
  m.querySelector('[name="name"]').value                     = row.name;
  m.querySelector('[name="description"]').value              = row.description;
  m.querySelector('[name="price"]').value                    = row.price;
  m.querySelector('[name="duration_mins"]').value            = row.duration_mins;
  m.querySelector('[name="is_featured"]').checked            = row.is_featured == 1;
  m.querySelector('[name="is_active"]').checked              = row.is_active == 1;
  openModal('editModal');
}
</script>

<?php require_once __DIR__ . '/../../includes/admin/admin_footer.php'; ?>