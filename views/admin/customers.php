<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/admin/auth.php';
requireAdmin();
$pageTitle = 'Customers';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = intval($_POST['id'] ?? 0);

    if ($action === 'toggle_active') {
        $val = intval($_POST['value']);
        $conn->query("UPDATE customers SET is_active=$val WHERE id=$id");
    }

    if ($action === 'delete') {
        $conn->query("DELETE FROM appointments WHERE customer_id=$id");
        $conn->query("DELETE FROM customers WHERE id=$id");
    }
}

// Filters
$search = $conn->real_escape_string($_GET['search'] ?? '');
$filter = $conn->real_escape_string($_GET['filter'] ?? 'all');

$where = ['1=1'];
if ($search) $where[] = "(c.first_name LIKE '%$search%' OR c.last_name LIKE '%$search%' OR c.email LIKE '%$search%' OR c.phone LIKE '%$search%')";
if ($filter === 'active')   $where[] = "c.is_active=1";
if ($filter === 'inactive') $where[] = "c.is_active=0";
$whereSQL = implode(' AND ', $where);

// Stats
$totalCustomers = $conn->query("SELECT COUNT(*) c FROM customers")->fetch_assoc()['c'];
$activeCustomers = $conn->query("SELECT COUNT(*) c FROM customers WHERE is_active=1")->fetch_assoc()['c'];
$newThisMonth   = $conn->query("SELECT COUNT(*) c FROM customers WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())")->fetch_assoc()['c'];

// Customers with booking stats
$customers = $conn->query(
    "SELECT c.*,
            COUNT(a.id) AS total_bookings,
            SUM(CASE WHEN a.status='completed' THEN 1 ELSE 0 END) AS completed,
            MAX(a.appointment_date) AS last_visit,
            COALESCE(SUM(CASE WHEN a.status='completed' THEN aps.price_snapshot ELSE 0 END),0) AS total_spent
     FROM customers c
     LEFT JOIN appointments a ON a.customer_id = c.id
     LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
     WHERE $whereSQL
     GROUP BY c.id
     ORDER BY c.created_at DESC"
);

require_once __DIR__ . '/../../includes/admin/admin_header.php';
?>

<!-- Stats -->
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon gold"><i class="fas fa-users"></i></div>
      <div class="stat-num"><?= number_format($totalCustomers) ?></div>
      <div class="stat-label">Total Customers</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon green"><i class="fas fa-user-check"></i></div>
      <div class="stat-num"><?= number_format($activeCustomers) ?></div>
      <div class="stat-label">Active</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="fas fa-user-plus"></i></div>
      <div class="stat-num"><?= number_format($newThisMonth) ?></div>
      <div class="stat-label">New This Month</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon black"><i class="fas fa-user-times"></i></div>
      <div class="stat-num"><?= number_format($totalCustomers - $activeCustomers) ?></div>
      <div class="stat-label">Inactive</div>
    </div>
  </div>
</div>

<!-- Filter Bar -->
<div class="admin-filter-bar mb-3">
  <form method="GET" class="d-flex gap-2 flex-wrap align-items-center w-100">
    <input type="text" name="search" class="admin-search"
           placeholder="Search name, email, phone..."
           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <select name="filter" class="admin-filter-select" onchange="this.form.submit()">
      <option value="all"      <?= $filter==='all'?'selected':'' ?>>All Customers</option>
      <option value="active"   <?= $filter==='active'?'selected':'' ?>>Active</option>
      <option value="inactive" <?= $filter==='inactive'?'selected':'' ?>>Inactive</option>
    </select>
    <button type="submit" class="btn-admin-dark">
      <i class="fas fa-search me-1"></i> Search
    </button>
    <a href="customers.php" class="btn-admin-outline">Reset</a>
  </form>
</div>

<!-- Customers Table -->
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Customer</th>
        <th>Contact</th>
        <th>Bookings</th>
        <th>Completed</th>
        <th>Total Spent</th>
        <th>Last Visit</th>
        <th>Status</th>
        <th>Joined</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($customers && $customers->num_rows > 0):
        while ($c = $customers->fetch_assoc()):
      ?>
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:36px;height:36px;border-radius:50%;background:var(--black);color:var(--gold);
                        display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;flex-shrink:0;">
              <?= strtoupper(substr($c['first_name'], 0, 1)) ?>
            </div>
            <div>
              <strong><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></strong>
            </div>
          </div>
        </td>
        <td>
          <div><?= htmlspecialchars($c['email']) ?></div>
          <div style="font-size:.78rem;color:var(--gray)"><?= htmlspecialchars($c['phone'] ?? '—') ?></div>
        </td>
        <td><strong><?= $c['total_bookings'] ?></strong></td>
        <td><?= $c['completed'] ?></td>
        <td><strong>₱<?= number_format((float)$c['total_spent']) ?></strong></td>
        <td>
          <?= $c['last_visit']
            ? date('M j, Y', strtotime($c['last_visit']))
            : '<span style="color:var(--gray-l);">Never</span>' ?>
        </td>
        <td>
          <form method="POST" style="display:inline;">
            <input type="hidden" name="action" value="toggle_active">
            <input type="hidden" name="id" value="<?= $c['id'] ?>">
            <input type="hidden" name="value" value="<?= $c['is_active'] ? 0 : 1 ?>">
            <button type="submit" class="badge-status <?= $c['is_active'] ? 'badge-confirmed' : 'badge-cancelled' ?>"
                    style="border:none;cursor:pointer;">
              <?= $c['is_active'] ? 'Active' : 'Inactive' ?>
            </button>
          </form>
        </td>
        <td style="font-size:.78rem;color:var(--gray)">
          <?= date('M j, Y', strtotime($c['created_at'])) ?>
        </td>
        <td>
          <div class="d-flex gap-1 flex-wrap">
            <button class="btn-sm-action confirm"
                    onclick="viewCustomer(this)"
                    data-customer="<?= htmlspecialchars(json_encode($c), ENT_QUOTES) ?>">
              <i class="fas fa-eye me-1"></i>View
            </button>
            <form method="POST" style="display:inline;"
                  onsubmit="return confirm('Delete this customer and all their bookings?')">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $c['id'] ?>">
              <button type="submit" class="btn-sm-action cancel">
                <i class="fas fa-trash"></i>
              </button>
            </form>
          </div>
        </td>
      </tr>
      <?php endwhile; else: ?>
      <tr>
        <td colspan="9" style="text-align:center;padding:40px;color:var(--gray);">
          No customers found.
        </td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Customer Detail Modal -->
<div class="admin-modal-backdrop" id="customerModal">
  <div class="admin-modal" style="max-width:600px;">
    <div class="admin-modal-header">
      <h5 id="modalCustomerName">Customer Details</h5>
      <button class="modal-close-btn" onclick="closeModal('customerModal')">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div class="admin-modal-body">

      <!-- Customer Info -->
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--light-2);">
        <div id="modalAvatar"
             style="width:56px;height:56px;border-radius:50%;background:var(--black);color:var(--gold);
                    display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;flex-shrink:0;">
        </div>
        <div>
          <div style="font-family:var(--font-h);font-size:1.1rem;color:var(--black);" id="modalName"></div>
          <div style="font-size:.82rem;color:var(--gray-d);" id="modalEmail"></div>
          <div style="font-size:.82rem;color:var(--gray-d);" id="modalPhone"></div>
        </div>
      </div>

      <!-- Stats -->
      <div style="display:flex;gap:0;border:1px solid var(--light-2);margin-bottom:20px;">
        <div style="flex:1;text-align:center;padding:14px;border-right:1px solid var(--light-2);">
          <div style="font-family:var(--font-h);font-size:1.5rem;color:var(--black);" id="modalBookings">0</div>
          <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:1px;color:var(--gray);">Bookings</div>
        </div>
        <div style="flex:1;text-align:center;padding:14px;border-right:1px solid var(--light-2);">
          <div style="font-family:var(--font-h);font-size:1.5rem;color:#15803d;" id="modalCompleted">0</div>
          <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:1px;color:var(--gray);">Completed</div>
        </div>
        <div style="flex:1;text-align:center;padding:14px;">
          <div style="font-family:var(--font-h);font-size:1.5rem;color:var(--gold-d);" id="modalSpent">₱0</div>
          <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:1px;color:var(--gray);">Total Spent</div>
        </div>
      </div>

      <!-- Recent Bookings -->
      <div style="font-size:.65rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gray-d);margin-bottom:10px;">
        Recent Bookings
      </div>
      <div id="modalBookingsList" style="font-size:.84rem;"></div>

    </div>
    <div class="admin-modal-footer">
      <button class="btn-admin-outline" onclick="closeModal('customerModal')">Close</button>
      <a id="modalViewAppts" href="#" class="btn-admin-gold">
        <i class="fas fa-calendar me-1"></i> View Appointments
      </a>
    </div>
  </div>
</div>

<script>
function openModal(id)  { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
document.querySelectorAll('.admin-modal-backdrop').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('active'); });
});

async function viewCustomer(btn) {
  const c = JSON.parse(btn.dataset.customer);
  document.getElementById('modalCustomerName').textContent = c.first_name + ' ' + c.last_name;
  document.getElementById('modalAvatar').textContent       = c.first_name.charAt(0).toUpperCase();
  document.getElementById('modalName').textContent         = c.first_name + ' ' + c.last_name;
  document.getElementById('modalEmail').textContent        = c.email;
  document.getElementById('modalPhone').textContent        = c.phone || '—';
  document.getElementById('modalBookings').textContent     = c.total_bookings;
  document.getElementById('modalCompleted').textContent    = c.completed;
  document.getElementById('modalSpent').textContent        = '₱' + parseInt(c.total_spent).toLocaleString();
  document.getElementById('modalViewAppts').href           = `/bg-barbershop/views/admin/appointments.php`;

  // Load recent bookings
  const listEl = document.getElementById('modalBookingsList');
  listEl.innerHTML = '<p style="color:var(--gray);">Loading...</p>';

  try {
    const res  = await fetch('/bg-barbershop/api/admin/get_customer_bookings.php?customer_id=' + c.id);
    const data = await res.json();
    if (data.bookings && data.bookings.length > 0) {
      listEl.innerHTML = data.bookings.map(b => `
        <div style="padding:10px 0;border-bottom:1px solid var(--light-2);display:flex;justify-content:space-between;">
          <div>
            <div style="font-weight:600;color:var(--black);">${b.services}</div>
            <div style="font-size:.75rem;color:var(--gray);">${b.date} · ${b.barber}</div>
          </div>
          <span style="font-size:.65rem;font-weight:700;text-transform:uppercase;padding:3px 8px;
                       background:${b.status==='completed'?'#dcfce7':b.status==='cancelled'?'#fee2e2':'#fef9c3'};
                       color:${b.status==='completed'?'#14532d':b.status==='cancelled'?'#7f1d1d':'#854d0e'};">
            ${b.status}
          </span>
        </div>
      `).join('');
    } else {
      listEl.innerHTML = '<p style="color:var(--gray);font-size:.85rem;">No bookings yet.</p>';
    }
  } catch(e) {
    listEl.innerHTML = '<p style="color:var(--gray);font-size:.85rem;">Could not load bookings.</p>';
  }

  openModal('customerModal');
}
</script>

<?php require_once __DIR__ . '/../../includes/admin/admin_footer.php'; ?>