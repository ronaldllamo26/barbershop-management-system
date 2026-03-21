<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/admin/auth.php';
requireAdmin();
$pageTitle = 'Reports';

// ── Date range filter ──
$range  = $_GET['range'] ?? 'this_month';
$custom_from = $_GET['from'] ?? '';
$custom_to   = $_GET['to']   ?? '';

switch ($range) {
    case 'today':
        $from = $to = date('Y-m-d'); break;
    case 'this_week':
        $from = date('Y-m-d', strtotime('monday this week'));
        $to   = date('Y-m-d', strtotime('sunday this week')); break;
    case 'last_month':
        $from = date('Y-m-01', strtotime('first day of last month'));
        $to   = date('Y-m-t',  strtotime('last day of last month')); break;
    case 'this_year':
        $from = date('Y-01-01');
        $to   = date('Y-12-31'); break;
    case 'custom':
        $from = $custom_from ?: date('Y-m-01');
        $to   = $custom_to   ?: date('Y-m-d'); break;
    default: // this_month
        $from = date('Y-m-01');
        $to   = date('Y-m-t');
}

$fromE = $conn->real_escape_string($from);
$toE   = $conn->real_escape_string($to);
$whereDate = "a.appointment_date BETWEEN '$fromE' AND '$toE'";

// ── Summary Stats ──
$totalBookings  = $conn->query("SELECT COUNT(*) c FROM appointments a WHERE $whereDate")->fetch_assoc()['c'];
$completed      = $conn->query("SELECT COUNT(*) c FROM appointments a WHERE $whereDate AND a.status='completed'")->fetch_assoc()['c'];
$cancelled      = $conn->query("SELECT COUNT(*) c FROM appointments a WHERE $whereDate AND a.status='cancelled'")->fetch_assoc()['c'];
$pending        = $conn->query("SELECT COUNT(*) c FROM appointments a WHERE $whereDate AND a.status='pending'")->fetch_assoc()['c'];

$revenue = $conn->query(
    "SELECT COALESCE(SUM(aps.price_snapshot),0) total
     FROM appointments a
     LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
     WHERE $whereDate AND a.status='completed'"
)->fetch_assoc()['total'];

$avgPerBooking = $completed > 0 ? $revenue / $completed : 0;

// ── Most Booked Services ──
$topServices = $conn->query(
    "SELECT s.name, COUNT(*) AS cnt, SUM(aps.price_snapshot) AS revenue
     FROM appointment_services aps
     JOIN appointments a ON a.id = aps.appointment_id
     JOIN services s ON s.id = aps.service_id
     WHERE $whereDate AND a.status != 'cancelled'
     GROUP BY s.id ORDER BY cnt DESC LIMIT 8"
);

// ── Barber Performance ──
$barberStats = $conn->query(
    "SELECT CONCAT(b.first_name,' ',b.last_name) AS name,
            b.role_title,
            COUNT(a.id) AS total,
            SUM(CASE WHEN a.status='completed' THEN 1 ELSE 0 END) AS done,
            SUM(CASE WHEN a.status='cancelled' THEN 1 ELSE 0 END) AS cancelled,
            COALESCE(SUM(CASE WHEN a.status='completed' THEN aps.price_snapshot ELSE 0 END),0) AS revenue
     FROM barbers b
     LEFT JOIN appointments a ON a.barber_id = b.id AND $whereDate
     LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
     GROUP BY b.id ORDER BY done DESC"
);

// ── Daily Bookings (for chart) ──
$dailyData = $conn->query(
    "SELECT appointment_date AS d, COUNT(*) AS cnt
     FROM appointments a
     WHERE $whereDate AND status != 'cancelled'
     GROUP BY appointment_date ORDER BY appointment_date ASC"
);
$chartLabels = [];
$chartData   = [];
while ($row = $dailyData->fetch_assoc()) {
    $chartLabels[] = date('M j', strtotime($row['d']));
    $chartData[]   = (int)$row['cnt'];
}

// ── Bookings by Day of Week ──
$dowData = $conn->query(
    "SELECT DAYNAME(appointment_date) AS dow,
            DAYOFWEEK(appointment_date) AS dow_num,
            COUNT(*) AS cnt
     FROM appointments a
     WHERE $whereDate AND status != 'cancelled'
     GROUP BY dow_num, dow ORDER BY dow_num"
);
$dowLabels = [];
$dowCounts = [];
while ($row = $dowData->fetch_assoc()) {
    $dowLabels[] = $row['dow'];
    $dowCounts[] = (int)$row['cnt'];
}

// ── Status breakdown for pie ──
$statusData = $conn->query(
    "SELECT status, COUNT(*) cnt FROM appointments a
     WHERE $whereDate GROUP BY status"
);
$statusLabels = [];
$statusCounts = [];
while ($row = $statusData->fetch_assoc()) {
    $statusLabels[] = ucfirst($row['status']);
    $statusCounts[] = (int)$row['cnt'];
}

require_once __DIR__ . '/../../includes/admin/admin_header.php';
?>

<!-- Date Range Filter -->
<div class="report-filter-bar">
  <form method="GET" class="d-flex flex-wrap align-items-center gap-2">
    <?php
    $ranges = ['today'=>'Today','this_week'=>'This Week','this_month'=>'This Month','last_month'=>'Last Month','this_year'=>'This Year','custom'=>'Custom'];
    foreach ($ranges as $val => $lbl):
      $active = $range === $val ? 'btn-admin-dark' : 'btn-admin-outline';
    ?>
    <button type="submit" name="range" value="<?= $val ?>" class="<?= $active ?>" style="padding:7px 16px;font-size:.68rem;">
      <?= $lbl ?>
    </button>
    <?php endforeach; ?>
    <?php if ($range === 'custom'): ?>
    <input type="date" name="from" class="admin-input" style="width:auto;padding:7px 12px;" value="<?= $from ?>">
    <span style="color:var(--gray);">to</span>
    <input type="date" name="to" class="admin-input" style="width:auto;padding:7px 12px;" value="<?= $to ?>">
    <button type="submit" name="range" value="custom" class="btn-admin-gold" style="padding:7px 16px;">Apply</button>
    <?php else: ?>
    <button type="submit" name="range" value="custom" class="btn-admin-outline" style="padding:7px 16px;font-size:.68rem;">Custom</button>
    <?php endif; ?>
  </form>
  <div class="report-date-label">
    <i class="fas fa-calendar-alt me-1" style="color:var(--gold-d)"></i>
    <?= date('M j, Y', strtotime($from)) ?> — <?= date('M j, Y', strtotime($to)) ?>
  </div>
</div>

<!-- Summary Stats -->
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon gold"><i class="fas fa-calendar-check"></i></div>
      <div class="stat-num"><?= number_format($totalBookings) ?></div>
      <div class="stat-label">Total Bookings</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
      <div class="stat-num"><?= number_format($completed) ?></div>
      <div class="stat-label">Completed</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon black"><i class="fas fa-peso-sign"></i></div>
      <div class="stat-num">₱<?= number_format($revenue) ?></div>
      <div class="stat-label">Total Revenue</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="fas fa-chart-line"></i></div>
      <div class="stat-num">₱<?= number_format($avgPerBooking) ?></div>
      <div class="stat-label">Avg per Booking</div>
    </div>
  </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">

  <!-- Daily Bookings Line Chart -->
  <div class="col-lg-8">
    <div class="report-card">
      <div class="report-card-header">
        <h5>Daily Bookings</h5>
        <span><?= date('M j', strtotime($from)) ?> – <?= date('M j, Y', strtotime($to)) ?></span>
      </div>
      <div class="report-card-body">
        <?php if (empty($chartLabels)): ?>
        <div class="report-empty"><i class="fas fa-chart-line"></i><p>No data for this period.</p></div>
        <?php else: ?>
        <canvas id="dailyChart" height="280"></canvas>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Status Pie -->
  <div class="col-lg-4">
    <div class="report-card">
      <div class="report-card-header">
        <h5>Booking Status</h5>
        <span>Breakdown</span>
      </div>
      <div class="report-card-body">
        <?php if (empty($statusLabels)): ?>
        <div class="report-empty"><i class="fas fa-chart-pie"></i><p>No data.</p></div>
        <?php else: ?>
        <canvas id="statusChart" height="280"></canvas>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>

<div class="row g-4 mb-4">

  <!-- Day of Week Bar Chart -->
  <div class="col-lg-6">
    <div class="report-card">
      <div class="report-card-header">
        <h5>Busiest Days of the Week</h5>
        <span>Total bookings per day</span>
      </div>
      <div class="report-card-body">
        <?php if (empty($dowLabels)): ?>
        <div class="report-empty"><i class="fas fa-chart-bar"></i><p>No data.</p></div>
        <?php else: ?>
        <canvas id="dowChart" height="260"></canvas>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Top Services -->
  <div class="col-lg-6">
    <div class="report-card">
      <div class="report-card-header">
        <h5>Most Booked Services</h5>
        <span>Top 8 services</span>
      </div>
      <div class="report-card-body" style="padding:0;">
        <?php if (!$topServices || $topServices->num_rows === 0): ?>
        <div class="report-empty"><i class="fas fa-cut"></i><p>No data.</p></div>
        <?php else:
          $maxCnt = 1;
          $rows = [];
          while ($r = $topServices->fetch_assoc()) { $rows[] = $r; if ($r['cnt'] > $maxCnt) $maxCnt = $r['cnt']; }
          foreach ($rows as $i => $r):
            $pct = round(($r['cnt'] / $maxCnt) * 100);
        ?>
        <div class="svc-report-row <?= $i % 2 === 0 ? 'even' : '' ?>">
          <div class="srr-rank"><?= $i+1 ?></div>
          <div class="srr-info">
            <span class="srr-name"><?= htmlspecialchars($r['name']) ?></span>
            <div class="srr-bar-wrap">
              <div class="srr-bar" style="width:<?= $pct ?>%"></div>
            </div>
          </div>
          <div class="srr-stats">
            <span class="srr-count"><?= $r['cnt'] ?>x</span>
            <span class="srr-rev">₱<?= number_format($r['revenue']) ?></span>
          </div>
        </div>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>

</div>

<!-- Barber Performance -->
<div class="report-card mb-4">
  <div class="report-card-header">
    <h5>Barber Performance</h5>
    <span><?= date('M j', strtotime($from)) ?> – <?= date('M j, Y', strtotime($to)) ?></span>
  </div>
  <div class="report-card-body" style="padding:0;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Barber</th>
          <th>Role</th>
          <th>Total Bookings</th>
          <th>Completed</th>
          <th>Cancelled</th>
          <th>Completion Rate</th>
          <th>Revenue Generated</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($b = $barberStats->fetch_assoc()):
          $rate = $b['total'] > 0 ? round(($b['done'] / $b['total']) * 100) : 0;
        ?>
        <tr>
          <td><strong><?= htmlspecialchars($b['name']) ?></strong></td>
          <td><?= htmlspecialchars($b['role_title']) ?></td>
          <td><?= $b['total'] ?></td>
          <td><span class="badge-status badge-confirmed"><?= $b['done'] ?></span></td>
          <td><?= $b['cancelled'] > 0 ? "<span class='badge-status badge-cancelled'>{$b['cancelled']}</span>" : '—' ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:8px;">
              <div style="flex:1;height:6px;background:var(--light-2);border-radius:3px;min-width:80px;">
                <div style="height:100%;width:<?= $rate ?>%;background:<?= $rate >= 80 ? '#22c55e' : ($rate >= 50 ? 'var(--gold)' : '#ef4444') ?>;border-radius:3px;"></div>
              </div>
              <span style="font-size:.8rem;font-weight:600;"><?= $rate ?>%</span>
            </div>
          </td>
          <td><strong>₱<?= number_format($b['revenue']) ?></strong></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const gold    = '#C9A84C';
const black   = '#0D0D0D';
const light   = '#F4F2EC';
const gray    = '#888880';

Chart.defaults.font.family = "'Raleway', sans-serif";
Chart.defaults.color       = '#555550';

// ── Daily Bookings ──
<?php if (!empty($chartLabels)): ?>
new Chart(document.getElementById('dailyChart'), {
  type: 'line',
  data: {
    labels: <?= json_encode($chartLabels) ?>,
    datasets: [{
      label: 'Bookings',
      data: <?= json_encode($chartData) ?>,
      borderColor: gold,
      backgroundColor: 'rgba(201,168,76,.08)',
      borderWidth: 2,
      pointBackgroundColor: gold,
      pointRadius: 4,
      tension: 0.4,
      fill: true,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,.05)' } },
      x: { grid: { display: false } }
    }
  }
});
<?php endif; ?>

// ── Status Pie ──
<?php if (!empty($statusLabels)): ?>
new Chart(document.getElementById('statusChart'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode($statusLabels) ?>,
    datasets: [{
      data: <?= json_encode($statusCounts) ?>,
      backgroundColor: ['#C9A84C','#22c55e','#3b82f6','#ef4444','#6b7280'],
      borderWidth: 0,
    }]
  },
  options: {
    responsive: true,
    cutout: '65%',
    plugins: {
      legend: { position: 'bottom', labels: { padding: 16, font: { size: 12 } } }
    }
  }
});
<?php endif; ?>

// ── Day of Week ──
<?php if (!empty($dowLabels)): ?>
new Chart(document.getElementById('dowChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($dowLabels) ?>,
    datasets: [{
      label: 'Bookings',
      data: <?= json_encode($dowCounts) ?>,
      backgroundColor: 'rgba(201,168,76,.75)',
      borderColor: gold,
      borderWidth: 1,
      borderRadius: 3,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,.05)' } },
      x: { grid: { display: false } }
    }
  }
});
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/../../includes/admin/admin_footer.php'; ?>