<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE_PATH . 'views/admin/login.php'); exit;
}

require_once __DIR__ . '/../../config/db.php';

$type   = $_GET['type']   ?? 'csv';
$status = $_GET['status'] ?? 'all';
$from   = $_GET['from']   ?? date('Y-m-01');
$to     = $_GET['to']     ?? date('Y-m-d');

$fromE = $conn->real_escape_string($from);
$toE   = $conn->real_escape_string($to);

$where = ["a.appointment_date BETWEEN '$fromE' AND '$toE'"];
if ($status !== 'all') $where[] = "a.status='" . $conn->real_escape_string($status) . "'";
$whereSQL = implode(' AND ', $where);

$result = $conn->query(
    "SELECT a.reference_no, a.guest_name, a.guest_phone, a.guest_email,
            a.appointment_date, a.start_time, a.end_time, a.status, a.payment_status,
            a.customer_notes, a.admin_notes,
            CONCAT(b.first_name,' ',b.last_name) AS barber,
            GROUP_CONCAT(s.name SEPARATOR ' + ') AS services,
            SUM(aps.price_snapshot) AS total_price,
            a.created_at
     FROM appointments a
     LEFT JOIN barbers b ON b.id = a.barber_id
     LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
     LEFT JOIN services s ON s.id = aps.service_id
     WHERE $whereSQL
     GROUP BY a.id
     ORDER BY a.appointment_date ASC, a.start_time ASC"
);

$rows = [];
while ($r = $result->fetch_assoc()) $rows[] = $r;

// ── CSV Export ──
if ($type === 'csv') {
    $filename = 'BG-Appointments-' . $from . '-to-' . $to . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');

    $out = fopen('php://output', 'w');
    // BOM for Excel UTF-8
    fputs($out, "\xEF\xBB\xBF");

    // Header row
    fputcsv($out, [
        'Reference #', 'Client Name', 'Phone', 'Email',
        'Date', 'Time', 'End Time', 'Barber',
        'Services', 'Total (PHP)', 'Status', 'Payment',
        'Notes', 'Admin Notes', 'Booked At'
    ]);

    foreach ($rows as $r) {
        fputcsv($out, [
            $r['reference_no'],
            $r['guest_name'],
            $r['guest_phone'],
            $r['guest_email'],
            date('M j, Y', strtotime($r['appointment_date'])),
            date('h:i A', strtotime($r['start_time'])),
            date('h:i A', strtotime($r['end_time'])),
            $r['barber'],
            $r['services'],
            $r['total_price'],
            ucfirst($r['status']),
            ucfirst($r['payment_status']),
            $r['customer_notes'],
            $r['admin_notes'],
            date('M j, Y h:i A', strtotime($r['created_at'])),
        ]);
    }
    fclose($out);
    exit;
}

// ── Print View ──
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>BG Barbershop — Appointments Report</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Arial', sans-serif; font-size: 12px; color: #111; background: #fff; }
    .print-header { padding: 24px 32px 16px; border-bottom: 3px solid #0D0D0D; display: flex; justify-content: space-between; align-items: flex-end; }
    .ph-brand { display: flex; align-items: center; gap: 12px; }
    .ph-logo  { font-size: 2rem; font-weight: 900; color: #C9A84C; letter-spacing: 2px; font-family: Georgia, serif; }
    .ph-name  { font-size: .75rem; color: #555; letter-spacing: 1px; text-transform: uppercase; }
    .ph-meta  { text-align: right; font-size: .75rem; color: #555; line-height: 1.8; }
    .ph-meta strong { color: #111; }

    .print-summary { display: flex; gap: 0; padding: 16px 32px; background: #f8f7f4; border-bottom: 1px solid #ddd; }
    .ps-item { flex: 1; text-align: center; padding: 8px; border-right: 1px solid #ddd; }
    .ps-item:last-child { border-right: none; }
    .ps-num   { font-size: 1.4rem; font-weight: 700; color: #0D0D0D; }
    .ps-label { font-size: .65rem; text-transform: uppercase; letter-spacing: 1px; color: #888; margin-top: 2px; }
    .ps-gold  { color: #C9A84C; }

    .print-content { padding: 20px 32px; }
    table { width: 100%; border-collapse: collapse; font-size: 11px; }
    thead th { background: #0D0D0D; color: #fff; padding: 8px 10px; text-align: left; font-size: .65rem; letter-spacing: 1px; text-transform: uppercase; white-space: nowrap; }
    thead th:first-child { color: #C9A84C; }
    tbody tr { border-bottom: 1px solid #eee; }
    tbody tr:nth-child(even) { background: #fafaf8; }
    tbody tr:hover { background: #f5f0e8; }
    td { padding: 8px 10px; vertical-align: top; }
    td strong { color: #0D0D0D; }
    .badge { display: inline-block; padding: 2px 7px; font-size: .62rem; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; border-radius: 2px; }
    .b-pending   { background: #fef9c3; color: #854d0e; }
    .b-confirmed { background: #dcfce7; color: #14532d; }
    .b-completed { background: #dbeafe; color: #1e3a8a; }
    .b-cancelled { background: #fee2e2; color: #7f1d1d; }
    .b-no_show   { background: #f1f5f9; color: #374151; }

    .print-footer { margin-top: 24px; padding: 14px 32px; border-top: 2px solid #0D0D0D; display: flex; justify-content: space-between; font-size: .7rem; color: #888; }

    @media print {
      body { font-size: 10px; }
      .no-print { display: none !important; }
      .print-header { padding: 16px 20px 12px; }
      .print-content { padding: 12px 20px; }
    }
  </style>
</head>
<body>

<div class="no-print" style="background:#0D0D0D;padding:12px 32px;display:flex;gap:12px;align-items:center;">
  <button onclick="window.print()" style="background:#C9A84C;color:#000;border:none;padding:8px 20px;font-weight:700;font-size:.8rem;letter-spacing:1px;text-transform:uppercase;cursor:pointer;">
    🖨 Print
  </button>
  <a href="?type=csv&status=<?= htmlspecialchars($status) ?>&from=<?= $from ?>&to=<?= $to ?>"
     style="background:#fff;color:#000;padding:8px 20px;font-weight:700;font-size:.8rem;letter-spacing:1px;text-transform:uppercase;text-decoration:none;">
    📥 Download CSV
  </a>
  <a href="/bg-barbershop/views/admin/appointments.php"
     style="color:#C9A84C;font-size:.8rem;margin-left:8px;">← Back to Appointments</a>
</div>

<!-- Header -->
<div class="print-header">
  <div class="ph-brand">
    <div class="ph-logo">BG</div>
    <div>
      <div style="font-weight:700;font-size:.95rem;">Biglang Gwapo Barbershop</div>
      <div class="ph-name">Appointments Report</div>
    </div>
  </div>
  <div class="ph-meta">
    <div><strong>Date Range:</strong> <?= date('M j, Y', strtotime($from)) ?> — <?= date('M j, Y', strtotime($to)) ?></div>
    <div><strong>Status:</strong> <?= $status === 'all' ? 'All' : ucfirst($status) ?></div>
    <div><strong>Generated:</strong> <?= date('M j, Y h:i A') ?></div>
    <div><strong>Total Records:</strong> <?= count($rows) ?></div>
  </div>
</div>

<!-- Summary -->
<?php
$totalRevenue  = array_sum(array_column($rows, 'total_price'));
$totalComplete = count(array_filter($rows, fn($r) => $r['status'] === 'completed'));
$totalPending  = count(array_filter($rows, fn($r) => $r['status'] === 'pending'));
$totalCancel   = count(array_filter($rows, fn($r) => $r['status'] === 'cancelled'));
?>
<div class="print-summary">
  <div class="ps-item"><div class="ps-num"><?= count($rows) ?></div><div class="ps-label">Total</div></div>
  <div class="ps-item"><div class="ps-num" style="color:#15803d"><?= $totalComplete ?></div><div class="ps-label">Completed</div></div>
  <div class="ps-item"><div class="ps-num" style="color:#854d0e"><?= $totalPending ?></div><div class="ps-label">Pending</div></div>
  <div class="ps-item"><div class="ps-num" style="color:#b91c1c"><?= $totalCancel ?></div><div class="ps-label">Cancelled</div></div>
  <div class="ps-item"><div class="ps-num ps-gold">₱<?= number_format($totalRevenue) ?></div><div class="ps-label">Revenue</div></div>
</div>

<!-- Table -->
<div class="print-content">
  <?php if (empty($rows)): ?>
  <p style="text-align:center;padding:40px;color:#888;">No appointments found for this period.</p>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Ref #</th>
        <th>Client</th>
        <th>Date & Time</th>
        <th>Barber</th>
        <th>Services</th>
        <th>Total</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
      <tr>
        <td style="font-family:monospace;color:#8A6820;font-size:.7rem;"><?= htmlspecialchars($r['reference_no']) ?></td>
        <td>
          <strong><?= htmlspecialchars($r['guest_name']) ?></strong><br>
          <span style="color:#888;font-size:.7rem;"><?= htmlspecialchars($r['guest_phone']) ?></span>
        </td>
        <td>
          <strong><?= date('M j, Y', strtotime($r['appointment_date'])) ?></strong><br>
          <span style="color:#888;"><?= date('h:i A', strtotime($r['start_time'])) ?></span>
        </td>
        <td><?= htmlspecialchars($r['barber']) ?></td>
        <td style="max-width:160px;"><?= htmlspecialchars($r['services'] ?? '—') ?></td>
        <td><strong>₱<?= number_format($r['total_price'] ?? 0) ?></strong></td>
        <td><span class="badge b-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<div class="print-footer">
  <span>BG Biglang Gwapo Barbershop — Confidential</span>
  <span>Printed: <?= date('M j, Y h:i A') ?></span>
</div>

</body>
</html>
<?php $conn->close(); ?>