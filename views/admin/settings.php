<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/admin/auth.php';
requireAdmin();
$pageTitle = 'Settings';

$alert = '';

// ── Save settings ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_general') {
        $fields = [
            'shop_name', 'shop_tagline', 'shop_address',
            'shop_phone', 'shop_email',
            'shop_hours_weekday', 'shop_hours_weekend',
        ];
        foreach ($fields as $key) {
            $val = $conn->real_escape_string(trim($_POST[$key] ?? ''));
            $conn->query("UPDATE settings SET setting_val='$val' WHERE setting_key='$key'");
        }
        $alert = 'success|General settings saved!';
    }

    if ($action === 'save_social') {
        $fields = ['facebook_url', 'instagram_url', 'tiktok_url'];
        foreach ($fields as $key) {
            $val = $conn->real_escape_string(trim($_POST[$key] ?? ''));
            $conn->query("UPDATE settings SET setting_val='$val' WHERE setting_key='$key'");
        }
        $alert = 'success|Social media links saved!';
    }

    if ($action === 'save_booking') {
        $fields = ['slot_interval_mins', 'booking_advance_days', 'google_maps_embed'];
        foreach ($fields as $key) {
            $val = $conn->real_escape_string(trim($_POST[$key] ?? ''));
            $conn->query("UPDATE settings SET setting_val='$val' WHERE setting_key='$key'");
        }
        $alert = 'success|Booking settings saved!';
    }

    if ($action === 'change_password') {
        $current  = $_POST['current_password'] ?? '';
        $new      = $_POST['new_password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $adminId  = currentAdmin()['id'];

        $res  = $conn->query("SELECT password FROM admins WHERE id=$adminId");
        $row  = $res->fetch_assoc();

        if (!password_verify($current, $row['password'])) {
            $alert = 'error|Current password is incorrect.';
        } elseif (strlen($new) < 8) {
            $alert = 'error|New password must be at least 8 characters.';
        } elseif ($new !== $confirm) {
            $alert = 'error|New passwords do not match.';
        } else {
            $hash = $conn->real_escape_string(password_hash($new, PASSWORD_BCRYPT, ['cost' => 10]));
            $conn->query("UPDATE admins SET password='$hash' WHERE id=$adminId");
            $alert = 'success|Password changed successfully!';
        }
    }
}

// ── Load all settings ──
$settingsRaw = $conn->query("SELECT setting_key, setting_val FROM settings");
$s = [];
while ($row = $settingsRaw->fetch_assoc()) {
    $s[$row['setting_key']] = $row['setting_val'];
}

function sv($s, $key) {
    return htmlspecialchars($s[$key] ?? '');
}

require_once __DIR__ . '/../../includes/admin/admin_header.php';

if ($alert) {
    [$type, $msg] = explode('|', $alert, 2);
    $icon = $type === 'success' ? 'check-circle' : 'exclamation-circle';
    echo "<div class='admin-alert $type'><i class='fas fa-$icon me-2'></i>$msg</div>";
}
?>

<div class="row g-4">

  <!-- ── General Settings ── -->
  <div class="col-lg-8">
    <div class="settings-card">
      <div class="settings-card-header">
        <i class="fas fa-store"></i>
        <div>
          <h4>General Settings</h4>
          <span>Shop info shown on the website</span>
        </div>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="save_general">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="admin-label">Shop Name</label>
            <input type="text" name="shop_name" class="admin-input" value="<?= sv($s,'shop_name') ?>">
          </div>
          <div class="col-md-6">
            <label class="admin-label">Tagline</label>
            <input type="text" name="shop_tagline" class="admin-input" value="<?= sv($s,'shop_tagline') ?>">
          </div>
          <div class="col-12">
            <label class="admin-label">Address</label>
            <input type="text" name="shop_address" class="admin-input" value="<?= sv($s,'shop_address') ?>">
          </div>
          <div class="col-md-6">
            <label class="admin-label">Phone / Viber</label>
            <input type="text" name="shop_phone" class="admin-input" value="<?= sv($s,'shop_phone') ?>">
          </div>
          <div class="col-md-6">
            <label class="admin-label">Email</label>
            <input type="email" name="shop_email" class="admin-input" value="<?= sv($s,'shop_email') ?>">
          </div>
          <div class="col-md-6">
            <label class="admin-label">Weekday Hours</label>
            <input type="text" name="shop_hours_weekday" class="admin-input"
                   value="<?= sv($s,'shop_hours_weekday') ?>" placeholder="9:00 AM – 8:00 PM">
          </div>
          <div class="col-md-6">
            <label class="admin-label">Weekend Hours</label>
            <input type="text" name="shop_hours_weekend" class="admin-input"
                   value="<?= sv($s,'shop_hours_weekend') ?>" placeholder="9:00 AM – 7:00 PM">
          </div>
        </div>
        <div class="settings-card-footer">
          <button type="submit" class="btn-admin-gold">
            <i class="fas fa-save me-1"></i> Save General Settings
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ── Right Column ── -->
  <div class="col-lg-4">

    <!-- Social Media -->
    <div class="settings-card mb-4">
      <div class="settings-card-header">
        <i class="fas fa-share-alt"></i>
        <div>
          <h4>Social Media</h4>
          <span>Links shown in footer & contact</span>
        </div>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="save_social">
        <div class="row g-3">
          <div class="col-12">
            <label class="admin-label"><i class="fab fa-facebook-f me-2" style="color:#1877f2"></i>Facebook URL</label>
            <input type="text" name="facebook_url" class="admin-input"
                   value="<?= sv($s,'facebook_url') ?>" placeholder="https://facebook.com/...">
          </div>
          <div class="col-12">
            <label class="admin-label"><i class="fab fa-instagram me-2" style="color:#e1306c"></i>Instagram URL</label>
            <input type="text" name="instagram_url" class="admin-input"
                   value="<?= sv($s,'instagram_url') ?>" placeholder="https://instagram.com/...">
          </div>
          <div class="col-12">
            <label class="admin-label"><i class="fab fa-tiktok me-2"></i>TikTok URL</label>
            <input type="text" name="tiktok_url" class="admin-input"
                   value="<?= sv($s,'tiktok_url') ?>" placeholder="https://tiktok.com/...">
          </div>
        </div>
        <div class="settings-card-footer">
          <button type="submit" class="btn-admin-gold">
            <i class="fas fa-save me-1"></i> Save Social Links
          </button>
        </div>
      </form>
    </div>

    <!-- Change Password -->
    <div class="settings-card">
      <div class="settings-card-header">
        <i class="fas fa-lock"></i>
        <div>
          <h4>Change Password</h4>
          <span>Update your admin password</span>
        </div>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="change_password">
        <div class="row g-3">
          <div class="col-12">
            <label class="admin-label">Current Password</label>
            <input type="password" name="current_password" class="admin-input" placeholder="••••••••" required>
          </div>
          <div class="col-12">
            <label class="admin-label">New Password</label>
            <input type="password" name="new_password" class="admin-input" placeholder="Min 8 characters" required>
          </div>
          <div class="col-12">
            <label class="admin-label">Confirm New Password</label>
            <input type="password" name="confirm_password" class="admin-input" placeholder="Repeat new password" required>
          </div>
        </div>
        <div class="settings-card-footer">
          <button type="submit" class="btn-admin-dark">
            <i class="fas fa-key me-1"></i> Change Password
          </button>
        </div>
      </form>
    </div>

  </div>

  <!-- ── Booking Settings ── -->
  <div class="col-lg-8">
    <div class="settings-card">
      <div class="settings-card-header">
        <i class="fas fa-calendar-cog"></i>
        <div>
          <h4>Booking Settings</h4>
          <span>Control how the booking system works</span>
        </div>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="save_booking">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="admin-label">Time Slot Interval (mins)</label>
            <select name="slot_interval_mins" class="admin-input">
              <?php foreach ([15,30,45,60] as $v): ?>
              <option value="<?= $v ?>" <?= ($s['slot_interval_mins']??'30')==$v?'selected':'' ?>><?= $v ?> minutes</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="admin-label">Max Days Ahead for Booking</label>
            <select name="booking_advance_days" class="admin-input">
              <?php foreach ([7,14,30,60,90] as $v): ?>
              <option value="<?= $v ?>" <?= ($s['booking_advance_days']??'30')==$v?'selected':'' ?>><?= $v ?> days</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12">
  <label class="admin-label">Google Maps Embed URL</label>
  <textarea name="google_maps_embed" class="admin-input" rows="3"
            placeholder="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3860.4200609056675!2d121.0772924759047!3d14.632080676303554!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b9b6890021e5%3A0x76d5ac6e7e6421f2!2sBiglang%20Gwapo%20Barbershop!5e0!3m2!1sen!2sph!4v1774065810296!5m2!1sen!2sph"><?= sv($s,'google_maps_embed') ?></textarea>
  <p style="font-size:.75rem;color:var(--gray);margin-top:6px;">
    <i class="fas fa-info-circle me-1"></i>
    I-paste dito yung URL lang — hindi yung buong iframe code.
  </p>
</div>
        </div>
        <div class="settings-card-footer">
          <button type="submit" class="btn-admin-gold">
            <i class="fas fa-save me-1"></i> Save Booking Settings
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ── System Info ── -->
  <div class="col-lg-4">
    <div class="settings-card">
      <div class="settings-card-header">
        <i class="fas fa-info-circle"></i>
        <div>
          <h4>System Info</h4>
          <span>Current environment details</span>
        </div>
      </div>
      <div class="system-info-list">
        <div class="si-row">
          <span>PHP Version</span>
          <strong><?= phpversion() ?></strong>
        </div>
        <div class="si-row">
          <span>MySQL Version</span>
          <strong><?= $conn->server_info ?></strong>
        </div>
        <div class="si-row">
          <span>Server</span>
          <strong><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Apache/XAMPP' ?></strong>
        </div>
        <div class="si-row">
          <span>Logged in as</span>
          <strong><?= htmlspecialchars(currentAdmin()['name']) ?></strong>
        </div>
        <div class="si-row">
          <span>Role</span>
          <strong><?= ucfirst(currentAdmin()['role']) ?></strong>
        </div>
        <div class="si-row">
          <span>Current Date</span>
          <strong><?= date('M j, Y') ?></strong>
        </div>
      </div>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/../../includes/admin/admin_footer.php'; ?>