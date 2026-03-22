<?php
// ============================================
//  BG Barbershop — Security Helper
//  Handles: CSRF, Rate Limiting, Sanitization,
//           Session Security, Input Validation
// ============================================

if (session_status() === PHP_SESSION_NONE) session_start();

// ── 1. CSRF Token ────────────────────────
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function csrf_verify() {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die(json_encode(['success' => false, 'message' => 'Invalid request. Please refresh and try again.']));
    }
}

// ── 2. Rate Limiting ─────────────────────
function rate_limit($key, $max, $window = 3600) {
    if (!isset($_SESSION['rate_limits'])) $_SESSION['rate_limits'] = [];

    $now  = time();
    $data = $_SESSION['rate_limits'][$key] ?? ['count' => 0, 'start' => $now];

    // Reset window if expired
    if ($now - $data['start'] > $window) {
        $data = ['count' => 0, 'start' => $now];
    }

    $data['count']++;
    $_SESSION['rate_limits'][$key] = $data;

    if ($data['count'] > $max) {
        $remaining = $window - ($now - $data['start']);
        $minutes   = ceil($remaining / 60);
        return [
            'blocked'   => true,
            'remaining' => $remaining,
            'message'   => "Too many attempts. Please wait {$minutes} minute(s) before trying again."
        ];
    }

    return ['blocked' => false, 'remaining' => 0];
}

function rate_limit_ip($action, $max, $window = 3600) {
    $ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = "rl_{$action}_{$ip}";
    return rate_limit($key, $max, $window);
}

// ── 3. Input Sanitization ────────────────
function clean($input) {
    if (is_array($input)) return array_map('clean', $input);
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function clean_email($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

function clean_int($val) {
    return intval($val);
}

function clean_float($val) {
    return floatval($val);
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validate_phone($phone) {
    // Philippine phone format: 09XXXXXXXXX or +639XXXXXXXXX
    $clean = preg_replace('/[\s\-\(\)]/', '', $phone);
    return preg_match('/^(\+?63|0)9\d{9}$/', $clean);
}

function validate_date($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// ── 4. Session Security ──────────────────
function secure_session_start() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.gc_maxlifetime', 7200); // 2 hours
        session_start();
    }

    // Regenerate session ID periodically to prevent fixation
    if (empty($_SESSION['last_regenerated'])) {
        session_regenerate_id(true);
        $_SESSION['last_regenerated'] = time();
    } elseif (time() - $_SESSION['last_regenerated'] > 1800) { // 30 mins
        session_regenerate_id(true);
        $_SESSION['last_regenerated'] = time();
    }

    // Admin session timeout — 2 hours of inactivity
    if (!empty($_SESSION['admin_id'])) {
        if (!empty($_SESSION['admin_last_activity'])) {
            if (time() - $_SESSION['admin_last_activity'] > 7200) {
                session_destroy();
                header('Location: /bg-barbershop/views/admin/login.php?timeout=1');
                exit;
            }
        }
        $_SESSION['admin_last_activity'] = time();
    }
}

// ── 5. XSS Protection Headers ───────────
function security_headers() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// ── 6. Honeypot field (catches bots) ─────
function honeypot_field() {
    // Bots fill in all fields — humans leave this empty
    return '<input type="text" name="website" style="display:none!important;position:absolute;left:-9999px;" tabindex="-1" autocomplete="off">';
}

function honeypot_check() {
    // If honeypot field is filled — it's a bot
    if (!empty($_POST['website'])) {
        http_response_code(403);
        die(json_encode(['success' => false, 'message' => 'Spam detected.']));
    }
}

// ── 7. Lockout System (Brute Force) ──────
function check_lockout($identifier) {
    $key      = 'lockout_' . md5($identifier);
    $attempts = $_SESSION[$key . '_attempts'] ?? 0;
    $locktime = $_SESSION[$key . '_locktime'] ?? 0;

    if ($locktime && time() < $locktime) {
        $remaining = ceil(($locktime - time()) / 60);
        return [
            'locked'    => true,
            'message'   => "Account temporarily locked. Try again in {$remaining} minute(s).",
            'remaining' => $locktime - time()
        ];
    }

    return ['locked' => false, 'attempts' => $attempts];
}

function record_failed_attempt($identifier, $max = 5, $lockout_mins = 15) {
    $key = 'lockout_' . md5($identifier);
    $_SESSION[$key . '_attempts'] = ($_SESSION[$key . '_attempts'] ?? 0) + 1;

    if ($_SESSION[$key . '_attempts'] >= $max) {
        $_SESSION[$key . '_locktime']  = time() + ($lockout_mins * 60);
        $_SESSION[$key . '_attempts']  = 0;
    }
}

function clear_failed_attempts($identifier) {
    $key = 'lockout_' . md5($identifier);
    unset($_SESSION[$key . '_attempts'], $_SESSION[$key . '_locktime']);
}

// ── 8. Booking Spam Prevention ───────────
function check_booking_spam($phone) {
    // Max 3 bookings per phone per day
    global $conn;
    if (!isset($conn)) return false;

    $today = date('Y-m-d');
    $p     = $conn->real_escape_string($phone);
    $res   = $conn->query(
        "SELECT COUNT(*) c FROM appointments
         WHERE guest_phone='$p'
           AND DATE(created_at)='$today'
           AND status != 'cancelled'"
    );
    $count = $res ? $res->fetch_assoc()['c'] : 0;
    return $count >= 3;
}

// ── 9. reCAPTCHA v3 ──────────────────────
define('RECAPTCHA_SITE_KEY',   '6Lef9plsAAAADlpJ9LESMH2t4rZE_qiuDK6P9lT');
define('RECAPTCHA_SECRET_KEY', '6Lef9plsAAAAFkLYyRvoErdMam3dmcJlwa4rDr1');
define('RECAPTCHA_MIN_SCORE',  0.5);

function recaptcha_script() {
    return '<script src="https://www.google.com/recaptcha/api.js?render=' . RECAPTCHA_SITE_KEY . '"></script>';
}

function recaptcha_field($action = 'submit') {
    return '
    <input type="hidden" name="recaptcha_token" id="recaptchaToken">
    <script>
    grecaptcha.ready(function() {
        grecaptcha.execute("' . RECAPTCHA_SITE_KEY . '", {action: "' . $action . '"}).then(function(token) {
            document.getElementById("recaptchaToken").value = token;
        });
    });
    </script>';
}

function recaptcha_verify($token) {
    if (empty($token)) return false;

    $response = file_get_contents(
        'https://www.google.com/recaptcha/api/siteverify?secret=' .
        RECAPTCHA_SECRET_KEY . '&response=' . urlencode($token)
    );

    if (!$response) return false;

    $data = json_decode($response, true);

    return isset($data['success']) && $data['success'] === true
        && isset($data['score'])   && $data['score'] >= RECAPTCHA_MIN_SCORE;
}