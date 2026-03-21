<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');

if (empty($_SESSION['admin_id'])) {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit;
}

require_once __DIR__ . '/../../config/db.php';
$data = json_decode(file_get_contents('php://input'), true);
$id   = intval($data['id'] ?? 0);

if (!$id) { echo json_encode(['success'=>false,'message'=>'Missing ID']); exit; }

$allowed = ['pending','confirmed','completed','cancelled','no_show'];
$sets    = [];

if (isset($data['status']) && in_array($data['status'], $allowed)) {
    $s      = $conn->real_escape_string($data['status']);
    $sets[] = "status='$s'";
}
if (isset($data['admin_notes'])) {
    $n      = $conn->real_escape_string($data['admin_notes']);
    $sets[] = "admin_notes='$n'";
}

if (empty($sets)) { echo json_encode(['success'=>false,'message'=>'Nothing to update']); exit; }

$sql = "UPDATE appointments SET " . implode(',', $sets) . " WHERE id=$id";
if ($conn->query($sql)) {
    // ── Send cancellation email if status changed to cancelled ──
    if (isset($data['status']) && $data['status'] === 'cancelled') {
        try {
            // Get appointment details
            $appt = $conn->query(
                "SELECT a.*, CONCAT(b.first_name,' ',b.last_name) AS barber_name
                 FROM appointments a
                 LEFT JOIN barbers b ON b.id = a.barber_id
                 WHERE a.id = $id LIMIT 1"
            )->fetch_assoc();

            if ($appt && !empty($appt['guest_email'])) {
                require_once __DIR__ . '/../../config/mailer.php';
                sendCancellationNotice(
                    $appt['guest_email'],
                    $appt['guest_name'],
                    $appt
                );
            }
        } catch (Throwable $e) {
            error_log('Cancellation email error: ' . $e->getMessage());
        }
    }
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'message'=>$conn->error]);
}
$conn->close();