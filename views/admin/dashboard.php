<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/admin/auth.php';
requireAdmin();
$pageTitle = 'Dashboard';

// ── Stats ──
$today = date('Y-m-d');

$totalToday   = $conn->query("SELECT COUNT(*) c FROM appointments WHERE appointment_date='$today' AND status NOT IN ('cancelled','no_show')")->fetch_assoc()['c'];
$totalPending = $conn->query("SELECT COUNT(*) c FROM appointments WHERE status='pending'")->fetch_assoc()['c'];
$totalMonth   = $conn->query("SELECT COUNT(*) c FROM appointments WHERE MONTH(appointment_date)=MONTH(NOW()) AND YEAR(appointment_date)=YEAR(NOW()) AND status='completed'")->fetch_assoc()['c'];
$totalClients = $conn->query("SELECT COUNT(DISTINCT guest_phone) c FROM appointments WHERE status!='cancelled'")->fetch_assoc()['c'];

// ── Today's appointments ──
$todayAppts = $conn->query(
  "SELECT a.*, b.first_name AS barber_fname, b.last_name AS barber_lname,
          GROUP_CONCAT(s.name SEPARATOR ', ') AS services
   FROM appointments a
   LEFT JOIN barbers b ON a.barber_id = b.id
   LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
   LEFT JOIN services s ON s.id = aps.service_id
   WHERE a.appointment_date = '$today'
   GROUP BY a.id
   ORDER BY a.start_time ASC"
);

// ── Recent bookings (last 10) ──
$recentAppts = $conn->query(
  "SELECT a.*, b.first_name AS barber_fname, b.last_name AS barber_lname,
          GROUP_CONCAT(s.name SEPARATOR ', ') AS services
   FROM appointments a
   LEFT JOIN barbers b ON a.barber_id = b.id
   LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
   LEFT JOIN services s ON s.id = aps.service_id
   GROUP BY a.id
   ORDER BY a.created_at DESC LIMIT 10"
);

require_once __DIR__ . '/../../includes/admin/admin_header.php';
?>

<!-- Stats Row -->
<div class="row g-3 mb-4">
  <?php
  $stats = [
    ['Today\'s Bookings', $totalToday,   'fa-calendar-day',   'gold'],
    ['Pending',           $totalPending, 'fa-clock',          'black'],
    ['Completed (Month)', $totalMonth,   'fa-check-circle',   'green'],
    ['Total Clients',     $totalClients, 'fa-users',          'blue'],
  ];
  foreach ($stats as [$label, $val, $icon, $color]): ?>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon <?= $color ?>"><i class="fas <?= $icon ?>"></i></div>
      <div class="stat-num"><?= number_format($val) ?></div>
      <div class="stat-label"><?= $label ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Today's Schedule -->
<div class="admin-section-header">
  <div class="admin-section-title">Today's Schedule <span style="color:var(--gray);font-size:.85rem;font-family:var(--font-b);font-weight:400;"><?= date('l, F j') ?></span></div>
  <a href="<?= BASE_PATH ?>views/admin/appointments.php" class="btn-admin-outline">
    <i class="fas fa-list me-1"></i> View All
  </a>
</div>

<div class="admin-table-wrap mb-4">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Time</th>
        <th>Client</th>
        <th>Service(s)</th>
        <th>Barber</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($todayAppts && $todayAppts->num_rows > 0):
        while ($row = $todayAppts->fetch_assoc()): ?>
      <tr>
        <td><strong><?= date('h:i A', strtotime($row['start_time'])) ?></strong></td>
        <td>
          <strong><?= htmlspecialchars($row['guest_name']) ?></strong><br>
          <span style="font-size:.75rem;color:var(--gray)"><?= htmlspecialchars($row['guest_phone']) ?></span>
        </td>
        <td><?= htmlspecialchars($row['services'] ?? '—') ?></td>
        <td><?= htmlspecialchars($row['barber_fname'] . ' ' . $row['barber_lname']) ?></td>
        <td><span class="badge-status badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
        <td>
          <div class="d-flex gap-1 flex-wrap">
            <?php if ($row['status'] === 'pending'): ?>
            <button class="btn-sm-action confirm" onclick="updateStatus(<?= $row['id'] ?>, 'confirmed')">Confirm</button>
            <?php endif; ?>
            <?php if (in_array($row['status'], ['pending','confirmed'])): ?>
            <button class="btn-sm-action complete" onclick="updateStatus(<?= $row['id'] ?>, 'completed')">Done</button>
            <button class="btn-sm-action cancel"  onclick="updateStatus(<?= $row['id'] ?>, 'cancelled')">Cancel</button>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--gray);">No appointments today.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Recent Bookings -->
<div class="admin-section-header">
  <div class="admin-section-title">Recent Bookings</div>
</div>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Ref #</th>
        <th>Client</th>
        <th>Service(s)</th>
        <th>Date</th>
        <th>Barber</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($recentAppts && $recentAppts->num_rows > 0):
        while ($row = $recentAppts->fetch_assoc()): ?>
      <tr>
        <td style="font-family:monospace;font-size:.78rem;color:var(--gold-d)"><?= $row['reference_no'] ?></td>
        <td>
          <strong><?= htmlspecialchars($row['guest_name']) ?></strong><br>
          <span style="font-size:.75rem;color:var(--gray)"><?= htmlspecialchars($row['guest_phone']) ?></span>
        </td>
        <td style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($row['services'] ?? '—') ?></td>
        <td><?= date('M j, Y', strtotime($row['appointment_date'])) ?><br>
            <span style="font-size:.75rem;color:var(--gray)"><?= date('h:i A', strtotime($row['start_time'])) ?></span></td>
        <td><?= htmlspecialchars($row['barber_fname'] . ' ' . $row['barber_lname']) ?></td>
        <td><span class="badge-status badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--gray);">No bookings yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
async function updateStatus(id, status) {
  const labels = { confirmed:'Confirm', completed:'Mark as Done', cancelled:'Cancel' };
  if (!confirm(`${labels[status]} this appointment?`)) return;
  try {
    const res  = await fetch('<?= BASE_PATH ?>api/admin/update_appointment.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, status })
    });
    const data = await res.json();
    if (data.success) location.reload();
    else alert('Error: ' + data.message);
  } catch { alert('Connection error.'); }
}
</script>

<?php require_once __DIR__ . '/../../includes/admin/admin_footer.php'; ?>