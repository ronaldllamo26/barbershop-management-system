<?php
require_once __DIR__ . '/config/db.php';

$sql = "CREATE TABLE IF NOT EXISTS walk_in_queue (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    customer_name   VARCHAR(100) NOT NULL,
    phone           VARCHAR(20),
    barber_id       INT NOT NULL,
    service_id      INT NOT NULL,
    service_name    VARCHAR(100),
    estimated_start TIME,
    estimated_end   TIME,
    status          ENUM('waiting','in_progress','done','cancelled') DEFAULT 'waiting',
    notes           TEXT,
    started_at      DATETIME NULL,
    completed_at    DATETIME NULL,
    created_at      DATETIME DEFAULT NOW(),
    FOREIGN KEY (barber_id)  REFERENCES barbers(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql)) {
    echo '<h2 style="font-family:sans-serif;color:green;">✓ walk_in_queue table created!</h2>';
    echo '<p><a href="views/admin/queue.php">→ Go to Queue</a></p>';
    echo '<p style="color:red;"><strong>⚠ DELETE this file now!</strong></p>';
} else {
    echo '<h2 style="font-family:sans-serif;color:red;">✗ Error: ' . $conn->error . '</h2>';
}
$conn->close();