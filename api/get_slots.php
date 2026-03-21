<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/db.php';

$barberId = intval($_GET['barber_id'] ?? 0);
$date     = $conn->real_escape_string($_GET['date'] ?? '');

if (!$barberId || !$date) {
    echo json_encode(['booked' => [], 'blocked' => false]);
    exit;
}

// Check if entire day is blocked (whole shop or this barber)
$dayBlock = $conn->query(
    "SELECT id FROM blocked_slots
     WHERE block_date = '$date'
       AND start_time IS NULL
       AND end_time IS NULL
       AND (barber_id IS NULL OR barber_id = $barberId)
     LIMIT 1"
);
if ($dayBlock && $dayBlock->num_rows > 0) {
    echo json_encode(['booked' => [], 'blocked' => true, 'message' => 'This day is fully blocked.']);
    exit;
}

// Get booked slots
$result = $conn->query(
    "SELECT TIME_FORMAT(start_time, '%H:%i') AS slot
     FROM appointments
     WHERE barber_id = $barberId
       AND appointment_date = '$date'
       AND status NOT IN ('cancelled','no_show')"
);
$booked = [];
while ($row = $result->fetch_assoc()) $booked[] = $row['slot'];

// Get partial time blocks (lunch breaks etc)
$partialBlocks = $conn->query(
    "SELECT TIME_FORMAT(start_time, '%H:%i') AS start,
            TIME_FORMAT(end_time,   '%H:%i') AS end
     FROM blocked_slots
     WHERE block_date = '$date'
       AND start_time IS NOT NULL
       AND end_time IS NOT NULL
       AND (barber_id IS NULL OR barber_id = $barberId)"
);
$blockedRanges = [];
while ($row = $partialBlocks->fetch_assoc()) {
    $blockedRanges[] = ['start' => $row['start'], 'end' => $row['end']];
}

echo json_encode([
    'booked'        => $booked,
    'blocked'       => false,
    'blocked_ranges'=> $blockedRanges,
    'date'          => $date,
    'barber_id'     => $barberId
]);
$conn->close();