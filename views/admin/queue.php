<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/admin/auth.php';
requireAdmin();
$pageTitle = 'Walk-in Queue';

// ── Handle Actions ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_walkin') {
        $name      = $conn->real_escape_string(trim($_POST['customer_name']));
        $phone     = $conn->real_escape_string(trim($_POST['phone'] ?? ''));
        $barberId  = intval($_POST['barber_id']);
        $serviceId = intval($_POST['service_id']);
        $notes     = $conn->real_escape_string(trim($_POST['notes'] ?? ''));

        // Get service duration
        $svcRes  = $conn->query("SELECT name, price, duration_mins FROM services WHERE id=$serviceId LIMIT 1");
        $svc     = $svcRes ? $svcRes->fetch_assoc() : ['name'=>'Walk-in','price'=>0,'duration_mins'=>30];
        $dur     = $svc['duration_mins'];

        // Calculate estimated start time based on queue
        $queueRes = $conn->query(
            "SELECT SUM(s.duration_mins) AS total_dur
             FROM walk_in_queue w
             LEFT JOIN services s ON s.id = w.service_id
             WHERE w.barber_id = $barberId
               AND w.status IN ('waiting','in_progress')
               AND DATE(w.created_at) = CURDATE()"
        );
        $qRow    = $queueRes ? $queueRes->fetch_assoc() : null;
        $totalDur = $qRow['total_dur'] ?? 0;
        $estStart = date('H:i', strtotime("+$totalDur minutes"));
        $estEnd   = date('H:i', strtotime("+" . ($totalDur + $dur) . " minutes"));

        $svcName  = $conn->real_escape_string($svc['name']);
        $price    = floatval($svc['price']);

        $conn->query(
            "INSERT INTO walk_in_queue
             (customer_name, phone, barber_id, service_id, service_name,
              estimated_start, estimated_end, status, notes, created_at)
             VALUES
             ('$name', '$phone', $barberId, $serviceId, '$svcName',
              '$estStart', '$estEnd', 'waiting', '$notes', NOW())"
        );
    }

    if ($action === 'update_status') {
        $id     = intval($_POST['id']);
        $status = $conn->real_escape_string($_POST['status']);
        $extra  = '';
        if ($status === 'in_progress') $extra = ", started_at = NOW()";
        if ($status === 'done')        $extra = ", completed_at = NOW()";
        $conn->query("UPDATE walk_in_queue SET status='$status' $extra WHERE id=$id");
    }

    if ($action === 'remove') {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM walk_in_queue WHERE id=$id");
    }

    if ($action === 'clear_done') {
        $conn->query("DELETE FROM walk_in_queue WHERE status='done' AND DATE(created_at) = CURDATE()");
    }
}

// ── Load queue (today only) ──
$queue = $conn->query(
    "SELECT w.*, b.first_name AS bfname, b.last_name AS blname, b.photo AS bphoto
     FROM walk_in_queue w
     LEFT JOIN barbers b ON b.id = w.barber_id
     WHERE DATE(w.created_at) = CURDATE()
     ORDER BY FIELD(w.status,'in_progress','waiting','done'), w.created_at ASC"
);

$barbers  = $conn->query("SELECT * FROM barbers WHERE is_active=1 ORDER BY sort_order");
$services = $conn->query(
    "SELECT s.*, sc.name AS cat_name FROM services s
     LEFT JOIN service_categories sc ON sc.id = s.category_id
     WHERE s.is_active=1 ORDER BY sc.sort_order, s.name"
);

// Stats for today
$stats = $conn->query(
    "SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status='waiting'     THEN 1 ELSE 0 END) AS waiting,
        SUM(CASE WHEN status='in_progress' THEN 1 ELSE 0 END) AS serving,
        SUM(CASE WHEN status='done'        THEN 1 ELSE 0 END) AS done
     FROM walk_in_queue WHERE DATE(created_at) = CURDATE()"
)->fetch_assoc();

require_once __DIR__ . '/../../includes/admin/admin_header.php';
?>

<!-- Auto refresh every 30 seconds -->
<script>setTimeout(() => location.reload(), 30000);</script>

<!-- Stats -->
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon gold"><i class="fas fa-users"></i></div>
      <div class="stat-num"><?= $stats['total'] ?? 0 ?></div>
      <div class="stat-label">Total Today</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon black"><i class="fas fa-chair"></i></div>
      <div class="stat-num"><?= $stats['serving'] ?? 0 ?></div>
      <div class="stat-label">In The Chair</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="fas fa-hourglass-half"></i></div>
      <div class="stat-num"><?= $stats['waiting'] ?? 0 ?></div>
      <div class="stat-label">Waiting</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon green"><i class="fas fa-check-double"></i></div>
      <div class="stat-num"><?= $stats['done'] ?? 0 ?></div>
      <div class="stat-label">Done Today</div>
    </div>
  </div>
</div>

<div class="row g-4">

  <!-- ── Queue Board ── -->
  <div class="col-lg-8">
    <div class="admin-section-header">
      <div class="admin-section-title">
        Today's Queue
        <span style="font-size:.75rem;font-family:var(--font-b);font-weight:400;color:var(--gray);margin-left:8px;">
          Auto-refreshes every 30s
        </span>
      </div>
      <?php if (($stats['done'] ?? 0) > 0): ?>
      <form method="POST" onsubmit="return confirm('Clear all done customers?')">
        <input type="hidden" name="action" value="clear_done">
        <button type="submit" class="btn-admin-outline">
          <i class="fas fa-broom me-1"></i> Clear Done
        </button>
      </form>
      <?php endif; ?>
    </div>

    <?php
    $rows = [];
    while ($r = $queue->fetch_assoc()) $rows[] = $r;

    if (empty($rows)):
    ?>
    <div style="background:var(--white);border:1px solid var(--light-2);padding:48px;text-align:center;color:var(--gray);">
      <i class="fas fa-store" style="font-size:2.5rem;margin-bottom:16px;display:block;color:var(--gray-l);"></i>
      <h4 style="font-family:var(--font-h);color:var(--black);margin-bottom:8px;">Queue is Empty</h4>
      <p style="font-size:.85rem;">Add a walk-in customer to get started!</p>
    </div>
    <?php else:
      // Separate by status
      $serving = array_filter($rows, fn($r) => $r['status'] === 'in_progress');
      $waiting = array_filter($rows, fn($r) => $r['status'] === 'waiting');
      $done    = array_filter($rows, fn($r) => $r['status'] === 'done');
    ?>

    <?php if (!empty($serving)): ?>
    <div class="queue-section-label in-progress">
      <i class="fas fa-cut me-2"></i> Currently In The Chair
    </div>
    <?php foreach ($serving as $r): ?>
    <div class="queue-card in-progress">
      <?php include __DIR__ . '/../../includes/admin/queue_card.php'; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($waiting)): ?>
    <div class="queue-section-label waiting">
      <i class="fas fa-hourglass-half me-2"></i> Waiting (<?= count($waiting) ?>)
    </div>
    <?php $pos = 1; foreach ($waiting as $r): ?>
    <div class="queue-card waiting">
      <div class="qc-position"><?= $pos++ ?></div>
      <?php include __DIR__ . '/../../includes/admin/queue_card.php'; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($done)): ?>
    <div class="queue-section-label done">
      <i class="fas fa-check-double me-2"></i> Done Today (<?= count($done) ?>)
    </div>
    <?php foreach ($done as $r): ?>
    <div class="queue-card done">
      <?php include __DIR__ . '/../../includes/admin/queue_card.php'; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <?php endif; ?>
  </div>

  <!-- ── Add Walk-in Form ── -->
  <div class="col-lg-4">
    <div class="settings-card">
      <div class="settings-card-header">
        <i class="fas fa-user-plus"></i>
        <div>
          <h4>Add Walk-in Customer</h4>
          <span>No appointment needed</span>
        </div>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="add_walkin">
        <div style="padding:20px;">
          <div class="row g-3">
            <div class="col-12">
              <label class="admin-label">Customer Name *</label>
              <input type="text" name="customer_name" class="admin-input"
                     placeholder="Juan Dela Cruz" required autofocus>
            </div>
            <div class="col-12">
              <label class="admin-label">Phone (optional)</label>
              <input type="text" name="phone" class="admin-input"
                     placeholder="+63 9XX XXX XXXX">
            </div>
            <div class="col-12">
              <label class="admin-label">Assign Barber *</label>
              <select name="barber_id" class="admin-input" required>
                <?php $barbers->data_seek(0);
                while ($b = $barbers->fetch_assoc()): ?>
                <option value="<?= $b['id'] ?>">
                  <?= htmlspecialchars($b['first_name'] . ' ' . $b['last_name']) ?>
                  — <?= htmlspecialchars($b['role_title']) ?>
                </option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="admin-label">Service *</label>
              <select name="service_id" class="admin-input" required>
                <?php
                $services->data_seek(0);
                $currentCat = '';
                while ($sv = $services->fetch_assoc()):
                  if ($sv['cat_name'] !== $currentCat):
                    if ($currentCat !== '') echo '</optgroup>';
                    echo '<optgroup label="' . htmlspecialchars($sv['cat_name']) . '">';
                    $currentCat = $sv['cat_name'];
                  endif;
                ?>
                <option value="<?= $sv['id'] ?>">
                  <?= htmlspecialchars($sv['name']) ?> — ₱<?= number_format($sv['price']) ?> (<?= $sv['duration_mins'] ?>mins)
                </option>
                <?php endwhile; echo '</optgroup>'; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="admin-label">Notes (optional)</label>
              <input type="text" name="notes" class="admin-input"
                     placeholder="e.g. Regular customer, prefers fade...">
            </div>
          </div>
        </div>
        <div class="settings-card-footer">
          <button type="submit" class="btn-admin-gold w-100">
            <i class="fas fa-plus me-2"></i> Add to Queue
          </button>
        </div>
      </form>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/../../includes/admin/admin_footer.php'; ?>