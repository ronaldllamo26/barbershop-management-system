<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/db.php';

$barberId = intval($_GET['barber_id'] ?? 0);
$date     = $conn->real_escape_string($_GET['date'] ?? '');

if (!$barberId || !$date) {
    echo json_encode(['booked' => []]);
    exit;
}

// Get all booked start times for this barber on this date
$result = $conn->query(
    "SELECT TIME_FORMAT(start_time, '%H:%i') AS slot
     FROM appointments
     WHERE barber_id = $barberId
       AND appointment_date = '$date'
       AND status NOT IN ('cancelled','no_show')"
);

$booked = [];
while ($row = $result->fetch_assoc()) {
    $booked[] = $row['slot'];
}

echo json_encode(['booked' => $booked, 'date' => $date, 'barber_id' => $barberId]);
$conn->close();