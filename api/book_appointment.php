<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    { echo json_encode(['success'=>false,'message'=>'Invalid method']); exit; }

// Start session to get customer_id if logged in
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/security.php';

security_headers();

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) { echo json_encode(['success'=>false,'message'=>'Invalid JSON payload.']); exit; }

// ── Rate limit booking — max 5 per IP per hour ──
$rate = rate_limit_ip('booking', 5, 3600);
if ($rate['blocked']) {
    echo json_encode(['success'=>false,'message'=>$rate['message']]); exit;
}

// ── Honeypot check ──
if (!empty($data['website'])) {
    echo json_encode(['success'=>false,'message'=>'Spam detected.']); exit;
}

// ── reCAPTCHA v3 verify ──
// Only verify if token is present — skip if empty (localhost testing)
$token = $data['recaptcha_token'] ?? '';
if (!empty($token) && !recaptcha_verify($token)) {
    echo json_encode(['success'=>false,'message'=>'Security check failed. Please refresh and try again.']); exit;
}

// ── Validate required fields ──
foreach (['services','barber','date','time','first_name','last_name','phone'] as $f) {
    if (empty($data[$f])) {
        echo json_encode(['success'=>false,'message'=>"Missing required field: $f"]); exit;
    }
}

// ── Validate date ──
if (!validate_date($data['date'])) {
    echo json_encode(['success'=>false,'message'=>'Invalid date format.']); exit;
}

// ── Validate date is not in the past ──
if ($data['date'] < date('Y-m-d')) {
    echo json_encode(['success'=>false,'message'=>'Cannot book appointments in the past.']); exit;
}

// ── Booking spam check — max 3 bookings per phone per day ──
if (!empty($data['phone']) && check_booking_spam($data['phone'])) {
    echo json_encode(['success'=>false,'message'=>'Maximum bookings per day reached for this phone number. Please contact us directly.']); exit;
}

// ── Sanitize inputs ──
$data['first_name'] = clean($data['first_name']);
$data['last_name']  = clean($data['last_name']);
$data['phone']      = clean($data['phone']);
$data['email']      = clean_email($data['email'] ?? '');
$data['notes']      = clean($data['notes'] ?? '');

$services = $data['services'];

// ── Determine customer_id ──
// Priority: 1) session (logged in), 2) sent via payload (logged in via PHP form), 3) null (guest)
$customerId = null;
if (!empty($_SESSION['customer_id'])) {
    $customerId = intval($_SESSION['customer_id']);
} elseif (!empty($data['customer_id'])) {
    $customerId = intval($data['customer_id']);
}

// ── Check barber conflict ──
$barberId = 0;
if ($data['barber'] !== 'No Preference') {
    $bname   = $conn->real_escape_string($data['barber']);
    $bResult = $conn->query("SELECT id FROM barbers WHERE CONCAT(first_name,' ',last_name)='$bname' LIMIT 1");
    if ($bResult && $bResult->num_rows > 0) $barberId = (int)$bResult->fetch_assoc()['id'];
    if (!$barberId) $barberId = 1;

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
    $barberId = 1;
}

// ── Calculate end time ──
$totalDur = array_sum(array_column($services, 'duration'));
$startTs  = strtotime($data['date'] . ' ' . $data['time']);
$end      = date('H:i', $startTs + $totalDur * 60);

// ── Generate reference no ──
$ref = 'BG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

$date_e  = $conn->real_escape_string($data['date']);
$time_e  = $conn->real_escape_string($data['time']);
$ref_e   = $conn->real_escape_string($ref);
$gname   = $conn->real_escape_string(trim($data['first_name'] . ' ' . $data['last_name']));
$phone   = $conn->real_escape_string($data['phone']);
$email   = $conn->real_escape_string($data['email'] ?? '');
$notes   = $conn->real_escape_string($data['notes'] ?? '');

// ── Build INSERT — include customer_id if logged in ──
if ($customerId) {
    $sql = "INSERT INTO appointments
            (reference_no, customer_id, guest_name, guest_phone, guest_email,
             barber_id, appointment_date, start_time, end_time, status, customer_notes)
            VALUES
            ('$ref_e', $customerId, '$gname', '$phone', '$email',
             $barberId, '$date_e', '$time_e', '$end', 'pending', '$notes')";
} else {
    $sql = "INSERT INTO appointments
            (reference_no, guest_name, guest_phone, guest_email,
             barber_id, appointment_date, start_time, end_time, status, customer_notes)
            VALUES
            ('$ref_e', '$gname', '$phone', '$email',
             $barberId, '$date_e', '$time_e', '$end', 'pending', '$notes')";
}

if (!$conn->query($sql)) {
    echo json_encode(['success'=>false,'message'=>'DB error: '.$conn->error]); exit;
}

$apptId = $conn->insert_id;

// ── Insert each service ──
foreach ($services as $svc) {
    $sname   = $conn->real_escape_string($svc['name']);
    $sResult = $conn->query("SELECT id, price FROM services WHERE name='$sname' LIMIT 1");
    $sRow    = $sResult && $sResult->num_rows > 0 ? $sResult->fetch_assoc() : ['id'=>1,'price'=>0];
    $conn->query("INSERT INTO appointment_services (appointment_id, service_id, price_snapshot)
                  VALUES ($apptId, {$sRow['id']}, {$sRow['price']})");
}

// ── Also update customer's phone if it was empty ──
if ($customerId && !empty($data['phone'])) {
    $ph = $conn->real_escape_string($data['phone']);
    $conn->query("UPDATE customers SET phone='$ph' WHERE id=$customerId AND (phone='' OR phone IS NULL)");
}

// ── Send emails ──
try {
    require_once __DIR__ . '/../config/mailer.php';
    $emailBooking = [
        'reference_no' => $ref,
        'services'     => $services,
        'barber'       => $data['barber'],
        'date'         => $data['date'],
        'time'         => $data['time'],
        'guest_name'   => $data['first_name'] . ' ' . $data['last_name'],
        'phone'        => $data['phone'],
    ];
    if (!empty($data['email'])) {
        sendBookingConfirmation($data['email'], $data['first_name'], $emailBooking);
    }
    sendAdminNewBooking($emailBooking);
} catch (Throwable $e) {
    error_log('Email error: ' . $e->getMessage());
}

echo json_encode(['success'=>true,'reference_no'=>$ref]);
$conn->close();