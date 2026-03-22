<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_id'])) {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit;
}

require_once __DIR__ . '/../../config/db.php';
$customerId = intval($_GET['customer_id'] ?? 0);

if (!$customerId) {
    echo json_encode(['bookings'=>[]]); exit;
}

$result = $conn->query(
    "SELECT a.reference_no, a.appointment_date, a.status,
            CONCAT(b.first_name,' ',b.last_name) AS barber,
            GROUP_CONCAT(s.name SEPARATOR ', ') AS services
     FROM appointments a
     LEFT JOIN barbers b ON b.id = a.barber_id
     LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
     LEFT JOIN services s ON s.id = aps.service_id
     WHERE a.customer_id = $customerId
     GROUP BY a.id
     ORDER BY a.appointment_date DESC
     LIMIT 5"
);

$bookings = [];
while ($r = $result->fetch_assoc()) {
    $bookings[] = [
        'ref'      => $r['reference_no'],
        'date'     => date('M j, Y', strtotime($r['appointment_date'])),
        'barber'   => $r['barber'],
        'services' => $r['services'] ?? '—',
        'status'   => $r['status'],
    ];
}

echo json_encode(['bookings' => $bookings]);
$conn->close();