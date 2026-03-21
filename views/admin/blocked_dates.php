<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/admin/auth.php';
requireAdmin();
$pageTitle = 'Block Dates';

$alert = '';

// ── Handle Actions ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $barberId  = $_POST['barber_id'] === 'all' ? 'NULL' : intval($_POST['barber_id']);
        $date      = $conn->real_escape_string($_POST['block_date']);
        $startTime = !empty($_POST['start_time']) ? "'" . $conn->real_escape_string($_POST['start_time']) . "'" : 'NULL';
        $endTime   = !empty($_POST['end_time'])   ? "'" . $conn->real_escape_string($_POST['end_time'])   . "'" : 'NULL';
        $reason    = $conn->real_escape_string(trim($_POST['reason'] ?? ''));
        $isFullDay = isset($_POST['full_day']);

        if ($isFullDay) { $startTime = 'NULL'; $endTime = 'NULL'; }

        $conn->query(
            "INSERT INTO blocked_slots (barber_id, block_date, start_time, end_time, reason)
             VALUES ($barberId, '$date', $startTime, $endTime, '$reason')"
        );
        $alert = 'success|Date blocked successfully!';
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM blocked_slots WHERE id=$id");
        $alert = 'success|Block removed.';
    }
}

// ── Load data ──
$barbers  = $conn->query("SELECT id, CONCAT(first_name,' ',last_name) AS name FROM barbers WHERE is_active=1 ORDER BY sort_order");
$upcoming = $conn->query(
    "SELECT bs.*, 
            CASE WHEN bs.barber_id IS NULL THEN 'All Barbers'
                 ELSE CONCAT(b.first_name,' ',b.last_name) END AS barber_name
     FROM blocked_slots bs
     LEFT JOIN barbers b ON b.id = bs.barber_id
     WHERE bs.block_date >= CURDATE()
     ORDER BY bs.block_date ASC, bs.start_time ASC"
);
$past = $conn->query(
    "SELECT bs.*,
            CASE WHEN bs.barber_id IS NULL THEN 'All Barbers'
                 ELSE CONCAT(b.first_name,' ',b.last_name) END AS barber_name
     FROM blocked_slots bs
     LEFT JOIN barbers b ON b.id = bs.barber_id
     WHERE bs.block_date < CURDATE()
     ORDER BY bs.block_date DESC LIMIT 20"
);

require_once __DIR__ . '/../../includes/admin/admin_header.php';

if ($alert) {
    [$type, $msg] = explode('|', $alert, 2);
    echo "<div class='admin-alert $type'><i class='fas fa-check-circle me-2'></i>$msg</div>";
}
?>

<div class="row g-4">

  <!-- ── Left: Add Block Form ── -->
  <div class="col-lg-4">
    <div class="settings-card">
      <div class="settings-card-header">
        <i class="fas fa-ban"></i>
        <div>
          <h4>Block a Date / Time</h4>
          <span>Set holidays, breaks, or rest days</span>
        </div>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="settings-card" style="border:none;box-shadow:none;padding:20px;">
          <div class="row g-3">

            <div class="col-12">
              <label class="admin-label">Block For</label>
              <select name="barber_id" class="admin-input" required>
                <option value="all">🏪 Entire Shop (All Barbers)</option>
                <?php
                $barbers->data_seek(0);
                while ($b = $barbers->fetch_assoc()): ?>
                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="col-12">
              <label class="admin-label">Date *</label>
              <input type="date" name="block_date" class="admin-input"
                     min="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="col-12">
              <label class="admin-label" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" name="full_day" id="fullDayCheck"
                       checked onchange="toggleTimeFields(this)"
                       style="width:16px;height:16px;">
                Full Day Block
              </label>
            </div>

            <div id="timeFields" style="display:none;" class="col-12">
              <div class="row g-2">
                <div class="col-6">
                  <label class="admin-label">Start Time</label>
                  <input type="time" name="start_time" class="admin-input" value="12:00">
                </div>
                <div class="col-6">
                  <label class="admin-label">End Time</label>
                  <input type="time" name="end_time" class="admin-input" value="13:00">
                </div>
              </div>
            </div>

            <div class="col-12">
              <label class="admin-label">Reason (optional)</label>
              <input type="text" name="reason" class="admin-input"
                     placeholder="e.g. Holiday, Lunch Break, Training...">
            </div>

          </div>
        </div>
        <div class="settings-card-footer">
          <button type="submit" class="btn-admin-gold">
            <i class="fas fa-ban me-1"></i> Block Date
          </button>
        </div>
      </form>
    </div>

    <!-- Quick Block Presets -->
    <div class="settings-card mt-3">
      <div class="settings-card-header">
        <i class="fas fa-bolt"></i>
        <div>
          <h4>Quick Presets</h4>
          <span>Common blocks</span>
        </div>
      </div>
      <div style="padding:16px;">
        <?php
        $presets = [
          ['🎄 Christmas Day',    date('Y') . '-12-25', 'Christmas Day'],
          ['🎆 New Year\'s Day',  date('Y') . '-01-01', 'New Year\'s Day'],
          ['💛 EDSA Anniversary', date('Y') . '-02-25', 'EDSA Anniversary'],
          ['🔥 Araw ng Kagitingan', date('Y') . '-04-09', 'Araw ng Kagitingan'],
          ['🇵🇭 Independence Day', date('Y') . '-06-12', 'Independence Day'],
          ['🦸 Bonifacio Day',    date('Y') . '-11-30', 'Bonifacio Day'],
          ['🎅 Rizal Day',        date('Y') . '-12-30', 'Rizal Day'],
        ];
        foreach ($presets as [$label, $date, $reason]): ?>
        <form method="POST" style="margin-bottom:8px;">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="barber_id" value="all">
          <input type="hidden" name="block_date" value="<?= $date ?>">
          <input type="hidden" name="full_day" value="1">
          <input type="hidden" name="reason" value="<?= htmlspecialchars($reason) ?>">
          <button type="submit" class="btn-admin-outline w-100" style="justify-content:flex-start;font-size:.72rem;">
            <?= $label ?>
            <span style="margin-left:auto;color:var(--gray);font-weight:400;"><?= date('M j', strtotime($date)) ?></span>
          </button>
        </form>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- ── Right: Blocked Dates List ── -->
  <div class="col-lg-8">

    <!-- Upcoming Blocks -->
    <div class="admin-section-header">
      <div class="admin-section-title">Upcoming Blocked Dates</div>
    </div>

    <div class="admin-table-wrap mb-4">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Barber / Shop</th>
            <th>Time</th>
            <th>Reason</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($upcoming && $upcoming->num_rows > 0):
            while ($row = $upcoming->fetch_assoc()):
              $isFullDay  = empty($row['start_time']) && empty($row['end_time']);
              $isShop     = $row['barber_id'] === null;
              $daysAway   = (int)((strtotime($row['block_date']) - strtotime('today')) / 86400);
          ?>
          <tr>
            <td>
              <strong><?= date('D, M j, Y', strtotime($row['block_date'])) ?></strong>
              <?php if ($daysAway === 0): ?>
              <span style="background:#fef9c3;color:#854d0e;font-size:.6rem;padding:2px 6px;font-weight:700;margin-left:6px;">TODAY</span>
              <?php elseif ($daysAway === 1): ?>
              <span style="background:#dbeafe;color:#1e3a8a;font-size:.6rem;padding:2px 6px;font-weight:700;margin-left:6px;">TOMORROW</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($isShop): ?>
              <span style="background:var(--black);color:var(--gold);font-size:.65rem;padding:3px 8px;font-weight:700;">🏪 Entire Shop</span>
              <?php else: ?>
              <?= htmlspecialchars($row['barber_name']) ?>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($isFullDay): ?>
              <span class="badge-status badge-cancelled">Full Day</span>
              <?php else: ?>
              <?= date('h:i A', strtotime($row['start_time'])) ?> – <?= date('h:i A', strtotime($row['end_time'])) ?>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['reason'] ?: '—') ?></td>
            <td>
              <form method="POST" onsubmit="return confirm('Remove this block?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <button type="submit" class="btn-sm-action cancel">
                  <i class="fas fa-trash me-1"></i>Remove
                </button>
              </form>
            </td>
          </tr>
          <?php endwhile; else: ?>
          <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--gray);">
            No upcoming blocked dates. 🎉
          </td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Past Blocks -->
    <div class="admin-section-header">
      <div class="admin-section-title" style="font-size:1rem;color:var(--gray-d);">Past Blocked Dates <span style="font-size:.75rem;font-family:var(--font-b);font-weight:400;">(last 20)</span></div>
    </div>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr><th>Date</th><th>Barber / Shop</th><th>Time</th><th>Reason</th></tr>
        </thead>
        <tbody>
          <?php if ($past && $past->num_rows > 0):
            while ($row = $past->fetch_assoc()):
              $isFullDay = empty($row['start_time']) && empty($row['end_time']);
          ?>
          <tr style="opacity:.6;">
            <td><?= date('D, M j, Y', strtotime($row['block_date'])) ?></td>
            <td><?= htmlspecialchars($row['barber_name']) ?></td>
            <td><?= $isFullDay ? 'Full Day' : date('h:i A', strtotime($row['start_time'])) . ' – ' . date('h:i A', strtotime($row['end_time'])) ?></td>
            <td><?= htmlspecialchars($row['reason'] ?: '—') ?></td>
          </tr>
          <?php endwhile; else: ?>
          <tr><td colspan="4" style="text-align:center;padding:20px;color:var(--gray);">No past blocks.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<script>
function toggleTimeFields(cb) {
  document.getElementById('timeFields').style.display = cb.checked ? 'none' : 'block';
}
</script>

<?php require_once __DIR__ . '/../../includes/admin/admin_footer.php'; ?>