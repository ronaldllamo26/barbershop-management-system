<?php
// ============================================
//  BG Barbershop — Mailer Helper
//  Requires PHPMailer in /vendor/phpmailer/
// ============================================

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/mail.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($to, $toName, $subject, $htmlBody) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to, $toName);
        $mail->addReplyTo(MAIL_FROM, MAIL_FROM_NAME);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '</p>'], "\n", $htmlBody));

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('BG Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}

// ── Email Templates ──────────────────────

function emailTemplate($title, $bodyContent) {
    return '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; background: #F4F2EC; color: #333; }
  .wrapper { max-width: 600px; margin: 32px auto; background: #fff; }
  .header { background: #0D0D0D; padding: 28px 32px; text-align: center; }
  .logo   { font-family: Georgia, serif; font-size: 2.5rem; font-weight: 900; color: #C9A84C; letter-spacing: 4px; }
  .logo-sub { font-size: .65rem; letter-spacing: 4px; text-transform: uppercase; color: #888; margin-top: 4px; }
  .gold-bar { height: 3px; background: #C9A84C; }
  .body   { padding: 32px; }
  .title  { font-family: Georgia, serif; font-size: 1.4rem; color: #0D0D0D; margin-bottom: 16px; }
  .text   { font-size: .9rem; color: #555; line-height: 1.7; margin-bottom: 16px; }
  .detail-box { background: #F4F2EC; border-left: 4px solid #C9A84C; padding: 20px 24px; margin: 20px 0; }
  .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #E8E5DE; font-size: .88rem; }
  .detail-row:last-child { border-bottom: none; }
  .detail-row span { color: #888; }
  .detail-row strong { color: #0D0D0D; text-align: right; max-width: 60%; }
  .ref-box { background: #0D0D0D; color: #C9A84C; text-align: center; padding: 16px; margin: 20px 0; font-family: monospace; font-size: 1.1rem; font-weight: 700; letter-spacing: 2px; }
  .btn { display: inline-block; background: #C9A84C; color: #000; padding: 12px 28px; text-decoration: none; font-weight: 700; font-size: .82rem; letter-spacing: 2px; text-transform: uppercase; margin: 8px 4px; }
  .footer { background: #0D0D0D; padding: 20px 32px; text-align: center; }
  .footer p { font-size: .72rem; color: #666; line-height: 1.8; }
  .footer a { color: #C9A84C; text-decoration: none; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <div class="logo">BG</div>
    <div class="logo-sub">Biglang Gwapo Barbershop</div>
  </div>
  <div class="gold-bar"></div>
  <div class="body">
    <h2 class="title">' . $title . '</h2>
    ' . $bodyContent . '
  </div>
  <div class="footer">
    <p>BG Biglang Gwapo Barbershop · Quezon City, Metro Manila<br>
    <a href="tel:+639123456789">+63 912 345 6789</a> · <a href="mailto:hello@bgbarbershop.com">hello@bgbarbershop.com</a><br>
    <span style="color:#444;font-size:.65rem;">This is an automated message. Please do not reply directly to this email.</span>
    </p>
  </div>
</div>
</body>
</html>';
}

// ── 1. Booking Confirmation ──────────────

function sendBookingConfirmation($toEmail, $toName, $booking) {
    if (!$toEmail) return false;

    $services = is_array($booking['services'])
        ? implode(', ', array_column($booking['services'], 'name'))
        : $booking['services'];

    $date    = date('l, F j, Y', strtotime($booking['date']));
    $time    = date('h:i A', strtotime($booking['time']));
    $total   = '₱' . number_format(array_sum(array_column(
        is_array($booking['services']) ? $booking['services'] : [], 'price'
    ) ?: [0]));

    $body = '
    <p class="text">Hi <strong>' . htmlspecialchars($toName) . '</strong>,<br>
    Your appointment at <strong>BG Biglang Gwapo Barbershop</strong> has been received! 🎉<br>
    We will confirm your slot shortly.</p>

    <div class="ref-box">Booking Ref: ' . htmlspecialchars($booking['reference_no']) . '</div>

    <div class="detail-box">
      <div class="detail-row"><span>Service(s)</span><strong>' . htmlspecialchars($services) . '</strong></div>
      <div class="detail-row"><span>Barber</span><strong>' . htmlspecialchars($booking['barber']) . '</strong></div>
      <div class="detail-row"><span>Date</span><strong>' . $date . '</strong></div>
      <div class="detail-row"><span>Time</span><strong>' . $time . '</strong></div>
    </div>

    <p class="text">Please arrive <strong>5 minutes early</strong>. If you need to cancel or reschedule, please contact us at least 2 hours before your appointment.</p>
    <p class="text" style="font-size:.8rem;color:#888;">📍 Quezon City, Metro Manila &nbsp;|&nbsp; 📞 +63 912 345 6789</p>';

    $html = emailTemplate('Booking Confirmed! ✓', $body);
    return sendMail($toEmail, $toName, 'BG Barbershop — Booking Confirmed (#' . $booking['reference_no'] . ')', $html);
}

// ── 2. Admin New Booking Notification ───

function sendAdminNewBooking($booking) {
    $services = is_array($booking['services'])
        ? implode(', ', array_column($booking['services'], 'name'))
        : $booking['services'];

    $date = date('D, M j, Y', strtotime($booking['date']));
    $time = date('h:i A', strtotime($booking['time']));

    $body = '
    <p class="text">A new appointment has been booked!</p>
    <div class="ref-box">' . htmlspecialchars($booking['reference_no']) . '</div>
    <div class="detail-box">
      <div class="detail-row"><span>Client</span><strong>' . htmlspecialchars($booking['guest_name']) . '</strong></div>
      <div class="detail-row"><span>Phone</span><strong>' . htmlspecialchars($booking['phone']) . '</strong></div>
      <div class="detail-row"><span>Service(s)</span><strong>' . htmlspecialchars($services) . '</strong></div>
      <div class="detail-row"><span>Barber</span><strong>' . htmlspecialchars($booking['barber']) . '</strong></div>
      <div class="detail-row"><span>Date</span><strong>' . $date . '</strong></div>
      <div class="detail-row"><span>Time</span><strong>' . $time . '</strong></div>
    </div>
    <p style="text-align:center;">
      <a href="http://localhost/bg-barbershop/views/admin/appointments.php" class="btn">View in Dashboard</a>
    </p>';

    $html = emailTemplate('New Booking Received!', $body);
    return sendMail(MAIL_ADMIN, 'BG Admin', 'New Booking — ' . $booking['guest_name'] . ' (' . $booking['reference_no'] . ')', $html);
}

// ── 3. Cancellation Notice ───────────────

function sendCancellationNotice($toEmail, $toName, $booking) {
    if (!$toEmail) return false;

    $date = date('l, F j, Y', strtotime($booking['appointment_date']));
    $time = date('h:i A', strtotime($booking['start_time']));

    $body = '
    <p class="text">Hi <strong>' . htmlspecialchars($toName) . '</strong>,<br>
    Your appointment has been <strong style="color:#b91c1c;">cancelled</strong>.</p>
    <div class="ref-box">' . htmlspecialchars($booking['reference_no']) . '</div>
    <div class="detail-box">
      <div class="detail-row"><span>Date</span><strong>' . $date . '</strong></div>
      <div class="detail-row"><span>Time</span><strong>' . $time . '</strong></div>
    </div>
    <p class="text">If you did not request this cancellation or would like to rebook, please contact us.</p>
    <p style="text-align:center;">
      <a href="http://localhost/bg-barbershop/views/user/booking.php" class="btn">Book Again</a>
    </p>';

    $html = emailTemplate('Appointment Cancelled', $body);
    return sendMail($toEmail, $toName, 'BG Barbershop — Appointment Cancelled (#' . $booking['reference_no'] . ')', $html);
}

// ── 4. Booking Reminder (1 day before) ──

function sendBookingReminder($toEmail, $toName, $booking) {
    if (!$toEmail) return false;

    $date    = date('l, F j, Y', strtotime($booking['appointment_date']));
    $time    = date('h:i A', strtotime($booking['start_time']));
    $barber  = $booking['barber_name'] ?? 'Your barber';
    $services = $booking['services'] ?? 'Your service';
    $ref     = $booking['reference_no'];

    $body = '
    <p class="text">Hi <strong>' . htmlspecialchars($toName) . '</strong>,<br>
    This is a friendly reminder that you have an appointment <strong>tomorrow</strong> at BG Biglang Gwapo Barbershop! 💈</p>

    <div class="ref-box">Ref: ' . htmlspecialchars($ref) . '</div>

    <div class="detail-box">
      <div class="detail-row"><span>Service(s)</span><strong>' . htmlspecialchars($services) . '</strong></div>
      <div class="detail-row"><span>Barber</span><strong>' . htmlspecialchars($barber) . '</strong></div>
      <div class="detail-row"><span>Date</span><strong>' . $date . '</strong></div>
      <div class="detail-row"><span>Time</span><strong>' . $time . '</strong></div>
    </div>

    <p class="text">
      📍 Please arrive <strong>5 minutes early</strong>.<br>
      Need to cancel? Please let us know at least 2 hours before your appointment.<br>
      📞 <strong>+63 912 345 6789</strong>
    </p>';

    $html = emailTemplate('Appointment Reminder — Tomorrow! 💈', $body);
    return sendMail($toEmail, $toName, 'BG Barbershop — Reminder: Appointment Tomorrow (#' . $ref . ')', $html);
}