<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    { echo json_encode(['success'=>false,'message'=>'Invalid method']); exit; }

require_once __DIR__ . '/../config/db.php';

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) { echo json_encode(['success'=>false,'message'=>'Invalid JSON payload.']); exit; }

// Validate required fields
foreach (['services','barber','date','time','first_name','last_name','phone'] as $f) {
    if (empty($data[$f])) {
        echo json_encode(['success'=>false,'message'=>"Missing: $f"]); exit;
    }
}

$services = $data['services']; // array of {name, price, duration}

// Check barber conflict (skip if No Preference)
$barberId = 0;
if ($data['barber'] !== 'No Preference') {
    $bname   = $conn->real_escape_string($data['barber']);
    $bResult = $conn->query("SELECT id FROM barbers WHERE CONCAT(first_name,' ',last_name)='$bname' LIMIT 1");
    if ($bResult && $bResult->num_rows > 0) $barberId = (int)$bResult->fetch_assoc()['id'];
    if (!$barberId) $barberId = 1;

    // Conflict check
    $date_e  = $conn->real_escape_string($data['date']);
    $time_e  = $conn->real_escape_string($data['time']);
    $conflict = $conn->query(
        "SELECT id FROM appointments
         WHERE barber_id=$barberId
           AND appointment_date='$date_e'
           AND start_time='$time_e'
           AND status NOT IN ('cancelled','no_show')
         LIMIT 1"
    );
    if ($conflict && $conflict->num_rows > 0) {
        echo json_encode(['success'=>false,'message'=>'Sorry, that time slot is already taken for this barber. Please choose another slot or barber.']);
        exit;
    }
} else {
    $barberId = 1; // assign default if no preference
}

// Calculate total duration & end time
$totalDur = array_sum(array_column($services, 'duration'));
$startTs  = strtotime($data['date'] . ' ' . $data['time']);
$end      = date('H:i', $startTs + $totalDur * 60);

// Generate reference no
$ref = 'BG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

$date_e  = $conn->real_escape_string($data['date']);
$time_e  = $conn->real_escape_string($data['time']);
$ref_e   = $conn->real_escape_string($ref);
$gname   = $conn->real_escape_string(trim($data['first_name'].' '.$data['last_name']));
$phone   = $conn->real_escape_string($data['phone']);
$email   = $conn->real_escape_string($data['email'] ?? '');
$notes   = $conn->real_escape_string($data['notes'] ?? '');

$sql = "INSERT INTO appointments
        (reference_no,guest_name,guest_phone,guest_email,barber_id,
         appointment_date,start_time,end_time,status,customer_notes)
        VALUES
        ('$ref_e','$gname','$phone','$email',$barberId,
         '$date_e','$time_e','$end','pending','$notes')";

if (!$conn->query($sql)) {
    echo json_encode(['success'=>false,'message'=>'DB error: '.$conn->error]); exit;
}

$apptId = $conn->insert_id;

// Insert each service
foreach ($services as $svc) {
    $sname   = $conn->real_escape_string($svc['name']);
    $sResult = $conn->query("SELECT id,price FROM services WHERE name='$sname' LIMIT 1");
    $sRow    = $sResult && $sResult->num_rows > 0 ? $sResult->fetch_assoc() : ['id'=>1,'price'=>0];
    $conn->query("INSERT INTO appointment_services (appointment_id,service_id,price_snapshot) VALUES ($apptId,{$sRow['id']},{$sRow['price']})");
}

echo json_encode(['success'=>true,'reference_no'=>$ref]);
$conn->close();