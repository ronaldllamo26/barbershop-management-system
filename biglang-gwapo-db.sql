-- ============================================================
--  BG – BIGLANG GWAPO BARBERSHOP
--  Database: biglang_gwapo
--  Import via: phpMyAdmin > Import > choose this file > Go
-- ============================================================

CREATE DATABASE IF NOT EXISTS biglang_gwapo
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE biglang_gwapo;

-- ── admins ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admins (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(100) NOT NULL,
  email      VARCHAR(150) NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  role       ENUM('superadmin','admin') NOT NULL DEFAULT 'admin',
  is_active  TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default login: admin@bgbarbershop.com / Admin@123  ← CHANGE AFTER SETUP
INSERT INTO admins (name, email, password, role) VALUES
('BG Admin', 'admin@bgbarbershop.com',
 '$2y$12$eImiTXuWVxfM37uY4JANjOe5XwL6p1vc2J9gHqTJ.6jGpBK1wjV6y', 'superadmin');

-- ── customers ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS customers (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name  VARCHAR(80)  NOT NULL,
  last_name   VARCHAR(80)  NOT NULL,
  email       VARCHAR(150) UNIQUE,
  phone       VARCHAR(20)  NOT NULL,
  password    VARCHAR(255) DEFAULT NULL,
  is_verified TINYINT(1) NOT NULL DEFAULT 0,
  is_active   TINYINT(1) NOT NULL DEFAULT 1,
  notes       TEXT DEFAULT NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── barbers ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS barbers (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name  VARCHAR(80)  NOT NULL,
  last_name   VARCHAR(80)  NOT NULL,
  role_title  VARCHAR(100) NOT NULL DEFAULT 'Barber',
  bio         TEXT DEFAULT NULL,
  photo       VARCHAR(255) DEFAULT NULL,
  instagram   VARCHAR(150) DEFAULT NULL,
  facebook    VARCHAR(150) DEFAULT NULL,
  is_active   TINYINT(1) NOT NULL DEFAULT 1,
  sort_order  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO barbers (first_name, last_name, role_title, photo, sort_order) VALUES
('Marco',  'Reyes',   'Head Barber',          'assets/images/barbers/barber-1.jpg', 1),
('Jake',   'Santos',  'Senior Barber',         'assets/images/barbers/barber-2.jpg', 2),
('Carlo',  'Mendoza', 'Fade Specialist',       'assets/images/barbers/barber-3.jpg', 3),
('Luis',   'Garcia',  'Color & Style Expert',  'assets/images/barbers/barber-4.jpg', 4);

-- ── service_categories ────────────────────────────────────
CREATE TABLE IF NOT EXISTS service_categories (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(100) NOT NULL,
  sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
  is_active  TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO service_categories (name, sort_order) VALUES
('Haircut & Style', 1),
('Shave',           2),
('Beard',           3),
('Treatment',       4),
('Kids',            5),
('Packages',        6);

-- ── services ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS services (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id   INT UNSIGNED NOT NULL,
  name          VARCHAR(150) NOT NULL,
  description   TEXT DEFAULT NULL,
  price         DECIMAL(8,2) NOT NULL,
  duration_mins SMALLINT UNSIGNED NOT NULL DEFAULT 30,
  is_featured   TINYINT(1) NOT NULL DEFAULT 0,
  is_active     TINYINT(1) NOT NULL DEFAULT 1,
  sort_order    TINYINT UNSIGNED NOT NULL DEFAULT 0,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO services (category_id, name, description, price, duration_mins, is_featured, sort_order) VALUES
(1,'Regular Haircut',         'Classic cut with your choice of style.',                          350.00,30,1,1),
(1,'Skin Fade',               'High, mid, or low fade — clean and precise.',                    400.00,35,1,2),
(1,'Textured Crop',           'Modern crop with textured top for a clean look.',                400.00,35,0,3),
(1,'Haircut + Blowdry',       'Full cut and professional blowdry finish.',                      500.00,45,0,4),
(2,'Hot Towel Shave',         'Full straight-razor shave with hot towel treatment.',            450.00,30,1,1),
(2,'Clean Shave',             'Quick clean shave with premium shaving cream.',                  250.00,20,0,2),
(3,'Beard Trim & Shape',      'Clean up and sculpt your beard to your desired style.',          250.00,20,1,1),
(3,'Beard Trim + Line Up',    'Full beard service with detailed line-up and shape.',            350.00,30,0,2),
(4,'Hot Oil Treatment',       'Deep conditioning with premium hair oil.',                       350.00,30,0,1),
(4,'Scalp Massage',           'Relaxing scalp massage to relieve stress and promote growth.',   300.00,20,0,2),
(4,'Anti-Dandruff Treatment', 'Medicated scalp treatment to eliminate dandruff.',               400.00,30,0,3),
(4,'Hair Color (Basic)',      'Single-process color application.',                              800.00,60,0,4),
(5,'Kids Haircut (12 & below)','Fun, gentle haircut for kids 12 years old and below.',         250.00,25,0,1),
(6,'BG Classic Package',      'Haircut + Hot Towel Shave. Best value combo.',                  750.00,55,1,1),
(6,'BG Premium Package',      'Haircut + Hot Towel Shave + Beard Trim + Scalp Massage.',       999.00,90,1,2),
(6,'BG Glow-Up Package',      'Haircut + Hair Treatment + Blowdry. Full refresh.',             900.00,75,0,3);

-- ── schedules (barber availability) ──────────────────────
CREATE TABLE IF NOT EXISTS schedules (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  barber_id    INT UNSIGNED NOT NULL,
  day_of_week  TINYINT UNSIGNED NOT NULL,  -- 0=Sun, 1=Mon ... 6=Sat
  start_time   TIME NOT NULL DEFAULT '09:00:00',
  end_time     TIME NOT NULL DEFAULT '20:00:00',
  is_available TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (barber_id) REFERENCES barbers(id) ON DELETE CASCADE,
  UNIQUE KEY uq_barber_day (barber_id, day_of_week)
) ENGINE=InnoDB;

INSERT INTO schedules (barber_id, day_of_week, start_time, end_time) VALUES
(1,0,'09:00','20:00'),(1,1,'09:00','20:00'),(1,2,'09:00','20:00'),(1,3,'09:00','20:00'),
(1,4,'09:00','20:00'),(1,5,'09:00','20:00'),(1,6,'09:00','19:00'),
(2,0,'09:00','20:00'),(2,1,'09:00','20:00'),(2,2,'09:00','20:00'),(2,3,'09:00','20:00'),
(2,4,'09:00','20:00'),(2,5,'09:00','20:00'),(2,6,'09:00','19:00'),
(3,0,'09:00','20:00'),(3,1,'09:00','20:00'),(3,2,'09:00','20:00'),(3,3,'09:00','20:00'),
(3,4,'09:00','20:00'),(3,5,'09:00','20:00'),(3,6,'09:00','19:00'),
(4,0,'09:00','20:00'),(4,1,'09:00','20:00'),(4,2,'09:00','20:00'),(4,3,'09:00','20:00'),
(4,4,'09:00','20:00'),(4,5,'09:00','20:00'),(4,6,'09:00','19:00');

-- ── appointments ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS appointments (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  reference_no     VARCHAR(20) NOT NULL UNIQUE,
  customer_id      INT UNSIGNED DEFAULT NULL,
  guest_name       VARCHAR(160) DEFAULT NULL,
  guest_phone      VARCHAR(20)  DEFAULT NULL,
  guest_email      VARCHAR(150) DEFAULT NULL,
  barber_id        INT UNSIGNED NOT NULL,
  appointment_date DATE NOT NULL,
  start_time       TIME NOT NULL,
  end_time         TIME NOT NULL,
  status           ENUM('pending','confirmed','completed','cancelled','no_show') NOT NULL DEFAULT 'pending',
  payment_status   ENUM('unpaid','paid','refunded') NOT NULL DEFAULT 'unpaid',
  customer_notes   TEXT DEFAULT NULL,
  admin_notes      TEXT DEFAULT NULL,
  created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
  FOREIGN KEY (barber_id)   REFERENCES barbers(id)   ON DELETE RESTRICT,
  INDEX idx_date   (appointment_date),
  INDEX idx_barber (barber_id),
  INDEX idx_status (status)
) ENGINE=InnoDB;

-- ── appointment_services ──────────────────────────────────
CREATE TABLE IF NOT EXISTS appointment_services (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  appointment_id INT UNSIGNED NOT NULL,
  service_id     INT UNSIGNED NOT NULL,
  price_snapshot DECIMAL(8,2) NOT NULL,
  FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
  FOREIGN KEY (service_id)     REFERENCES services(id)     ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ── blocked_slots ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS blocked_slots (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  barber_id  INT UNSIGNED DEFAULT NULL,
  block_date DATE NOT NULL,
  start_time TIME DEFAULT NULL,
  end_time   TIME DEFAULT NULL,
  reason     VARCHAR(200) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (barber_id) REFERENCES barbers(id) ON DELETE CASCADE,
  INDEX idx_block_date (block_date)
) ENGINE=InnoDB;

-- ── gallery ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS gallery (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(150) DEFAULT NULL,
  image_path  VARCHAR(255) NOT NULL,
  category    ENUM('haircut','beard','shave','shop','team','before_after') NOT NULL DEFAULT 'haircut',
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_active   TINYINT(1) NOT NULL DEFAULT 1,
  sort_order  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO gallery (title, image_path, category, is_featured, sort_order) VALUES
('Skin Fade',        'assets/images/gallery/g1.jpg', 'haircut',    1, 1),
('Textured Crop',    'assets/images/gallery/g2.jpg', 'haircut',    1, 2),
('Hot Towel Shave',  'assets/images/gallery/g3.jpg', 'shave',      1, 3),
('Beard Sculpt',     'assets/images/gallery/g4.jpg', 'beard',      0, 4),
('Hair Treatment',   'assets/images/gallery/g5.jpg', 'haircut',    0, 5);

-- ── testimonials ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS testimonials (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(120) NOT NULL,
  location      VARCHAR(100) DEFAULT NULL,
  review_text   TEXT NOT NULL,
  rating        TINYINT UNSIGNED NOT NULL DEFAULT 5,
  is_featured   TINYINT(1) NOT NULL DEFAULT 0,
  is_active     TINYINT(1) NOT NULL DEFAULT 1,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO testimonials (customer_name, location, review_text, rating, is_featured) VALUES
('Jerico P.', 'Cubao',        'Best barbershop in QC, walang tanong. Marco knows exactly what I want. Consistent talo pa yung mga kilala.', 5, 1),
('Aldrin M.', 'Quezon City',  'Dali mag-book, malinis yung lugar, at yung barbers professional talaga. Worth it ang presyo.', 5, 1),
('Renzo D.',  'Diliman',      'First time ko dito, sinabihan ako ng friend ko. Hindi ako disappointed. Yung hot towel shave experience, ibang klase talaga.', 5, 1);

-- ── settings ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS settings (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_val TEXT DEFAULT NULL,
  label       VARCHAR(150) DEFAULT NULL,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO settings (setting_key, setting_val, label) VALUES
('shop_name',          'BG Biglang Gwapo Barbershop', 'Shop Name'),
('shop_tagline',       'Look Sharp. Feel Unstoppable.','Tagline'),
('shop_address',       '123 Sample Street, Quezon City, Metro Manila', 'Address'),
('shop_phone',         '+63 912 345 6789',             'Phone'),
('shop_email',         'hello@bgbarbershop.com',       'Email'),
('shop_hours_weekday', '9:00 AM – 8:00 PM',            'Weekday Hours'),
('shop_hours_weekend', '9:00 AM – 7:00 PM',            'Weekend Hours'),
('facebook_url',       '',                             'Facebook URL'),
('instagram_url',      '',                             'Instagram URL'),
('tiktok_url',         '',                             'TikTok URL'),
('slot_interval_mins', '30',                           'Booking Slot Interval (mins)'),
('booking_advance_days','30',                          'Max Days Ahead for Booking'),
('google_maps_embed',  '',                             'Google Maps Embed URL');
