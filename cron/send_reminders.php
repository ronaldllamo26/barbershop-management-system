<?php
// ============================================
//  BG Barbershop — Booking Reminder Cron
//  Runs daily at 8:00 AM via Task Scheduler
//  Sends reminder to customers with appt tomorrow
// ============================================

define('BASE_PATH', '/bg-barbershop/');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/mailer.php';

$tomorrow = date('Y-m-d', strtotime('+1 day'));
$log      = [];
$sent     = 0;
$failed   = 0;

$result = $conn->query(
    "SELECT a.*,
            CONCAT(b.first_name,' ',b.last_name) AS barber_name,
            GROUP_CONCAT(s.name SEPARATOR ', ') AS services
     FROM appointments a
     LEFT JOIN barbers b ON b.id = a.barber_id
     LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
     LEFT JOIN services s ON s.id = aps.service_id
     WHERE a.appointment_date = '$tomorrow'
       AND a.status IN ('pending','confirmed')
       AND a.guest_email != ''
       AND a.guest_email IS NOT NULL
       AND (a.reminder_sent IS NULL OR a.reminder_sent = 0)
     GROUP BY a.id"
);

if (!$result) {
    echo "DB Error: " . $conn->error . "\n"; exit;
}

$log[] = "=== BG Barbershop Reminder Cron ===";
$log[] = "Date: " . date('Y-m-d H:i:s');
$log[] = "Sending reminders for: $tomorrow";
$log[] = "Found: " . $result->num_rows . " appointment(s)";
$log[] = "";

while ($appt = $result->fetch_assoc()) {
    $sent_ok = sendBookingReminder(
        $appt['guest_email'],
        $appt['guest_name'],
        $appt
    );
    if ($sent_ok) {
        $id = intval($appt['id']);
        $conn->query("UPDATE appointments SET reminder_sent=1 WHERE id=$id");
        $log[] = "✓ Sent to: {$appt['guest_name']} ({$appt['guest_email']})";
        $sent++;
    } else {
        $log[] = "✗ Failed: {$appt['guest_name']} ({$appt['guest_email']})";
        $failed++;
    }
}

$log[] = "";
$log[] = "Done! Sent: $sent | Failed: $failed";
$log[] = str_repeat("=", 40);

$logFile = __DIR__ . '/reminder_log.txt';
file_put_contents($logFile, implode("\n", $log) . "\n\n", FILE_APPEND);
echo implode("\n", $log);
$conn->close();