<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/admin/auth.php';
requireAdmin();
$pageTitle = 'Appointments';

// Filters
$statusFilter = $conn->real_escape_string($_GET['status'] ?? 'all');
$dateFilter   = $conn->real_escape_string($_GET['date']   ?? '');
$search       = $conn->real_escape_string($_GET['search'] ?? '');

$where = ['1=1'];
if ($statusFilter !== 'all')  $where[] = "a.status='$statusFilter'";
if ($dateFilter)               $where[] = "a.appointment_date='$dateFilter'";
if ($search)                   $where[] = "(a.guest_name LIKE '%$search%' OR a.guest_phone LIKE '%$search%' OR a.reference_no LIKE '%$search%')";
$whereSQL = implode(' AND ', $where);

$appointments = $conn->query(
  "SELECT a.*, b.first_name AS bfname, b.last_name AS blname,
          GROUP_CONCAT(s.name SEPARATOR ', ') AS services,
          SUM(aps.price_snapshot) AS total_price
   FROM appointments a
   LEFT JOIN barbers b ON a.barber_id = b.id
   LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
   LEFT JOIN services s ON s.id = aps.service_id
   WHERE $whereSQL
   GROUP BY a.id
   ORDER BY a.appointment_date DESC, a.start_time DESC"
);

require_once __DIR__ . '/../../includes/admin/admin_header.php';
?>

<!-- Filter Bar -->
<div class="admin-filter-bar">
  <form method="GET" class="d-flex gap-2 flex-wrap align-items-center w-100">
    <input type="text" name="search" class="admin-search"
           placeholder="Search name, phone, ref#..."
           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <select name="status" class="admin-filter-select" onchange="this.form.submit()">
      <?php foreach (['all'=>'All Status','pending'=>'Pending','confirmed'=>'Confirmed','completed'=>'Completed','cancelled'=>'Cancelled','no_show'=>'No Show'] as $val => $lbl): ?>
      <option value="<?= $val ?>" <?= ($statusFilter===$val)?'selected':'' ?>><?= $lbl ?></option>
      <?php endforeach; ?>
    </select>
    <input type="date" name="date" class="admin-filter-select"
           value="<?= htmlspecialchars($_GET['date'] ?? '') ?>"
           onchange="this.form.submit()">
    <button type="submit" class="btn-admin-dark"><i class="fas fa-search me-1"></i> Search</button>
    <!-- Export Buttons -->
    <?php
    $exportParams = http_build_query([
        'status' => $_GET['status'] ?? 'all',
        'from'   => $_GET['date']   ?? date('Y-m-01'),
        'to'     => $_GET['date']   ?? date('Y-m-d'),
    ]);
    ?>
    <div class="ms-auto d-flex gap-2">
      <a href="/bg-barbershop/api/admin/export_appointments.php?type=print&<?= $exportParams ?>"
         target="_blank" class="btn-admin-outline" title="Print View">
        <i class="fas fa-print me-1"></i> Print
      </a>
      <a href="/bg-barbershop/api/admin/export_appointments.php?type=csv&<?= $exportParams ?>"
         class="btn-admin-gold" title="Export to CSV">
        <i class="fas fa-file-csv me-1"></i> Export CSV
      </a>
    </div>
    <a href="appointments.php" class="btn-admin-outline">Reset</a>
  </form>
</div>

<!-- Table -->
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Ref #</th>
        <th>Client</th>
        <th>Service(s)</th>
        <th>Date & Time</th>
        <th>Barber</th>
        <th>Total</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($appointments && $appointments->num_rows > 0):
        while ($row = $appointments->fetch_assoc()): ?>
      <tr id="row-<?= $row['id'] ?>">
        <td style="font-family:monospace;font-size:.75rem;color:var(--gold-d);"><?= $row['reference_no'] ?></td>
        <td>
          <strong><?= htmlspecialchars($row['guest_name']) ?></strong><br>
          <span style="font-size:.75rem;color:var(--gray)"><?= htmlspecialchars($row['guest_phone']) ?></span>
          <?php if ($row['guest_email']): ?>
          <br><span style="font-size:.72rem;color:var(--gray)"><?= htmlspecialchars($row['guest_email']) ?></span>
          <?php endif; ?>
        </td>
        <td style="max-width:180px;"><?= htmlspecialchars($row['services'] ?? '—') ?></td>
        <td>
          <strong><?= date('M j, Y', strtotime($row['appointment_date'])) ?></strong><br>
          <span style="font-size:.78rem;color:var(--gray)">
            <?= date('h:i A', strtotime($row['start_time'])) ?> – <?= date('h:i A', strtotime($row['end_time'])) ?>
          </span>
        </td>
        <td><?= htmlspecialchars($row['bfname'] . ' ' . $row['blname']) ?></td>
        <td><strong>₱<?= number_format($row['total_price'] ?? 0) ?></strong></td>
        <td><span class="badge-status badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
        <td>
          <div class="d-flex gap-1 flex-wrap">
            <?php if ($row['status'] === 'pending'): ?>
            <button class="btn-sm-action confirm"  onclick="updateStatus(<?= $row['id'] ?>,'confirmed')">Confirm</button>
            <?php endif; ?>
            <?php if (in_array($row['status'], ['pending','confirmed'])): ?>
            <button class="btn-sm-action complete" onclick="updateStatus(<?= $row['id'] ?>,'completed')">Done</button>
            <button class="btn-sm-action cancel"   onclick="updateStatus(<?= $row['id'] ?>,'cancelled')">Cancel</button>
            <?php endif; ?>
            <?php if ($row['status'] !== 'no_show' && in_array($row['status'], ['pending','confirmed'])): ?>
            <button class="btn-sm-action"          onclick="updateStatus(<?= $row['id'] ?>,'no_show')">No Show</button>
            <?php endif; ?>
            <button class="btn-sm-action" onclick="viewNotes(<?= $row['id'] ?>, '<?= addslashes($row['customer_notes'] ?? '') ?>', '<?= addslashes($row['admin_notes'] ?? '') ?>')">Notes</button>
          </div>
        </td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--gray);">No appointments found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Notes Modal -->
<div class="admin-modal-backdrop" id="notesModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h5>Appointment Notes</h5>
      <button class="modal-close-btn" onclick="closeModal('notesModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="admin-modal-body">
      <div id="modalAlert"></div>
      <input type="hidden" id="notesApptId">
      <div class="mb-3">
        <label class="admin-label">Customer Notes</label>
        <textarea id="customerNotes" class="admin-input" rows="3" readonly style="background:var(--light);"></textarea>
      </div>
      <div>
        <label class="admin-label">Admin Notes</label>
        <textarea id="adminNotes" class="admin-input" rows="3" placeholder="Add internal notes..."></textarea>
      </div>
    </div>
    <div class="admin-modal-footer">
      <button class="btn-admin-outline" onclick="closeModal('notesModal')">Close</button>
      <button class="btn-admin-gold" onclick="saveNotes()"><i class="fas fa-save me-1"></i> Save Notes</button>
    </div>
  </div>
</div>

<script>
async function updateStatus(id, status) {
  const labels = { confirmed:'Confirm', completed:'Mark as Done', cancelled:'Cancel', no_show:'Mark as No Show' };
  if (!confirm(`${labels[status]} this appointment?`)) return;
  try {
    const res  = await fetch('<?= BASE_PATH ?>api/admin/update_appointment.php', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, status })
    });
    const data = await res.json();
    if (data.success) location.reload();
    else alert('Error: ' + data.message);
  } catch { alert('Connection error.'); }
}

function viewNotes(id, customerNotes, adminNotes) {
  document.getElementById('notesApptId').value   = id;
  document.getElementById('customerNotes').value  = customerNotes || '(none)';
  document.getElementById('adminNotes').value     = adminNotes || '';
  document.getElementById('modalAlert').innerHTML = '';
  openModal('notesModal');
}

async function saveNotes() {
  const id    = document.getElementById('notesApptId').value;
  const notes = document.getElementById('adminNotes').value;
  try {
    const res  = await fetch('<?= BASE_PATH ?>api/admin/update_appointment.php', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, admin_notes: notes })
    });
    const data = await res.json();
    const el   = document.getElementById('modalAlert');
    if (data.success) {
      el.innerHTML = '<div class="admin-alert success"><i class="fas fa-check me-2"></i>Notes saved!</div>';
    } else {
      el.innerHTML = `<div class="admin-alert error">${data.message}</div>`;
    }
  } catch { alert('Connection error.'); }
}

function openModal(id)  { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
document.querySelectorAll('.admin-modal-backdrop').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('active'); });
});
</script>

<?php require_once __DIR__ . '/../../includes/admin/admin_footer.php'; ?>