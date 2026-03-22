# 💈 BG Biglang Gwapo Barbershop — System

<div align="center">

![BG Barbershop](https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=1200&h=300&q=80&fit=crop)

**A full-stack web-based barbershop management and online booking system**

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![PHPMailer](https://img.shields.io/badge/PHPMailer-7.0-EA4335?style=flat-square&logo=gmail&logoColor=white)

</div>

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Folder Structure](#folder-structure)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Security](#security)
- [Email System](#email-system)
- [Admin Panel](#admin-panel)
- [Screenshots](#screenshots)

---

## 🏪 Overview

**BG Biglang Gwapo Barbershop** is a complete web-based management system built for a barbershop in Quezon City, Metro Manila. It covers everything from online booking to admin operations, walk-in queue management, email notifications, and reporting.

> **Local URL:** `http://localhost/bg-barbershop/`
> **Root Path:** `C:/xampp/htdocs/bg-barbershop/`

---

## ✨ Features

### 👤 Customer Side
- 🗓️ **Online Booking** — 4-step booking form with conflict detection and real-time availability
- 📧 **Email Confirmation** — Automatic confirmation email after booking
- 🖨️ **Booking Confirmation Page** — Printable receipt with all appointment details
- 👤 **Customer Accounts** — Register, login, view booking history, edit profile
- 📱 **Mobile Responsive** — Works on all screen sizes
- 🖼️ **Gallery** — Masonry photo grid with lightbox and category filter
- ⭐ **Reviews** — Customer testimonials displayed on homepage

### 🔧 Admin Side
- 📊 **Dashboard** — Today's schedule, stats, recent bookings
- 📅 **Appointments** — Full CRUD with status management, notes, export to CSV, print
- ✂️ **Services** — Manage services and categories with pricing
- 💈 **Barbers** — Staff profiles with photos and social links
- 🖼️ **Gallery** — Upload and manage gallery photos
- 📈 **Reports** — Revenue, booking trends, barber performance with charts
- 🚫 **Block Dates** — Block specific dates/times for holidays or breaks
- 🏪 **Walk-in Queue** — Real-time queue management with status tracking
- ⭐ **Reviews** — Manage customer testimonials
- 👥 **Customers** — View registered customers with booking stats
- ⚙️ **Settings** — Shop info, hours, social links, email config

### 📧 Email Notifications
- ✅ Booking confirmation to customer
- 🔔 New booking notification to admin
- ❌ Cancellation notice to customer
- ⏰ Daily reminder (1 day before appointment via Task Scheduler)

---

## 🛠️ Tech Stack

| Category | Technology |
|----------|-----------|
| Frontend | HTML5, CSS3, Bootstrap 5.3, JavaScript |
| Backend  | PHP 8.2 |
| Database | MySQL / MariaDB |
| Email    | PHPMailer 7.0 + Gmail SMTP |
| Security | reCAPTCHA v3, CSRF, Rate Limiting, bcrypt |
| Charts   | Chart.js 4.4 |
| Calendar | Flatpickr |
| Icons    | Font Awesome 6.5 |
| Fonts    | Playfair Display, Raleway (Google Fonts) |
| Server   | Apache / XAMPP |

---

## 📁 Folder Structure

```
bg-barbershop/
├── index.php                          # Root entry point
├── biglang-gwapo-db.sql               # Full database schema
├── composer.json                      # PHP dependencies
├── vendor/                            # Composer packages (PHPMailer)
├── cron/
│   ├── send_reminders.php             # Daily reminder cron script
│   └── run_reminders.bat              # Windows Task Scheduler runner
├── config/
│   ├── db.php                         # Database connection
│   ├── mail.php                       # Email credentials
│   ├── mailer.php                     # Email templates & functions
│   └── security.php                   # Security helper functions
├── includes/
│   ├── header.php                     # HTML head + CSS links
│   ├── navbar.php                     # Navigation bar
│   ├── footer.php                     # Footer + scripts
│   └── admin/
│       ├── admin_header.php           # Admin sidebar + topbar
│       ├── admin_footer.php           # Admin scripts
│       ├── auth.php                   # Admin authentication
│       ├── queue_card.php             # Walk-in queue card partial
│       ├── service_form_fields.php    # Service form partial
│       ├── barber_form_fields.php     # Barber form partial
│       └── review_form_fields.php     # Review form partial
├── views/
│   ├── user/
│   │   ├── index.php                  # Homepage
│   │   ├── services.php               # Services page
│   │   ├── booking.php                # 4-step booking form
│   │   ├── booking_confirmation.php   # Printable booking receipt
│   │   ├── gallery.php                # Photo gallery
│   │   ├── about.php                  # About page
│   │   ├── contact.php                # Contact page
│   │   ├── login.php                  # Customer login
│   │   ├── register.php               # Customer registration
│   │   ├── my_bookings.php            # Customer booking history
│   │   ├── profile.php                # Customer profile edit
│   │   └── logout.php                 # Customer logout
│   └── admin/
│       ├── login.php                  # Admin login
│       ├── logout.php                 # Admin logout
│       ├── dashboard.php              # Main dashboard
│       ├── appointments.php           # Appointments management
│       ├── services.php               # Services management
│       ├── barbers.php                # Barbers management
│       ├── gallery.php                # Gallery management
│       ├── reports.php                # Reports & analytics
│       ├── blocked_dates.php          # Block dates management
│       ├── queue.php                  # Walk-in queue
│       ├── reviews.php                # Reviews management
│       ├── customers.php              # Customer management
│       └── settings.php              # System settings
├── api/
│   ├── book_appointment.php           # Booking submission API
│   ├── get_slots.php                  # Available slots API
│   └── admin/
│       ├── update_appointment.php     # Update appointment status
│       ├── export_appointments.php    # CSV/print export
│       └── get_customer_bookings.php  # Customer bookings API
└── assets/
    ├── css/
    │   ├── style.css                  # Main stylesheet
    │   ├── pages.css                  # Page-specific styles
    │   └── admin.css                  # Admin panel styles
    ├── js/
    │   └── main.js                    # Main JavaScript
    └── images/
        ├── barbers/                   # Local barber photos
        ├── gallery/                   # Uploaded gallery images
        └── logo/                      # Logo and favicon
```

---

## 🚀 Installation

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) with PHP 8.2+ and MySQL
- [Composer](https://getcomposer.org/) for PHP packages
- Internet connection (for Google Fonts, CDN libraries, reCAPTCHA)

### Steps

**1. Clone or copy the project**
```bash
# Copy bg-barbershop folder to:
C:/xampp/htdocs/bg-barbershop/
```

**2. Create the database**
```
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Create database: biglang_gwapo
3. Import: biglang-gwapo-db.sql
```

**3. Install PHP dependencies**
```bash
cd C:/xampp/htdocs/bg-barbershop
composer require phpmailer/phpmailer
```

**4. Configure the application**
```bash
# Edit config/db.php — set your database credentials
# Edit config/mail.php — set your Gmail credentials
```

**5. Access the system**
```
User Site:  http://localhost/bg-barbershop/
Admin Panel: http://localhost/bg-barbershop/views/admin/login.php
```

---

## ⚙️ Configuration

### Database (`config/db.php`)
```php
$conn = new mysqli('localhost', 'root', '', 'biglang_gwapo');
```

### Email (`config/mail.php`)
```php
define('MAIL_HOST',     'smtp.gmail.com');
define('MAIL_PORT',     587);
define('MAIL_USERNAME', 'your-gmail@gmail.com');
define('MAIL_PASSWORD', 'your-16-char-app-password');
define('MAIL_FROM',     'your-gmail@gmail.com');
define('MAIL_FROM_NAME','BG Biglang Gwapo Barbershop');
define('MAIL_ADMIN',    'admin@gmail.com');
```

> **Note:** Gmail App Password is required. Enable 2-Step Verification first, then generate an App Password at `myaccount.google.com > Security > App Passwords`.

### reCAPTCHA (`config/security.php`)
```php
define('RECAPTCHA_SITE_KEY',   'your-site-key');
define('RECAPTCHA_SECRET_KEY', 'your-secret-key');
define('RECAPTCHA_MIN_SCORE',  0.5);
```

> Get your keys at: https://www.google.com/recaptcha/admin

---

## 📖 Usage

### Default Admin Credentials
| Field    | Value                      |
|----------|---------------------------|
| Email    | admin@bgbarbershop.com    |
| Password | Admin@123                 |
| Role     | superadmin                |

> ⚠️ **Change these credentials after first login!**

### Booking Flow
```
Customer → Select Services → Choose Barber → Pick Schedule → Enter Info → Confirm
                                                                              ↓
                                                              Confirmation Email Sent
                                                              Booking Confirmation Page
```

### Admin Workflow
```
New Booking → Admin Notified via Email → Review in Dashboard
                                              ↓
                                    Confirm / Cancel / Complete
                                              ↓
                               Customer notified via Email
```

### Walk-in Queue Flow
```
Customer Arrives → Staff Adds to Queue → Barber Starts → Mark as Done
      ↓                                      ↓
Add: Name, Service,              Status: Waiting → In Progress → Done
     Barber, Notes               Auto-refresh every 30 seconds
```

---

## 🔒 Security

| Feature | Details |
|---------|---------|
| **CSRF Protection** | Token on every form, validated server-side |
| **Honeypot** | Hidden field catches bots automatically |
| **Brute Force** | 5 failed attempts = 15-min lockout |
| **Rate Limiting** | Max 5 bookings/hour per IP, max 10 logins/hour per IP |
| **Booking Spam** | Max 3 bookings per phone number per day |
| **Input Sanitization** | All inputs sanitized before DB insertion |
| **Session Security** | Regenerated every 30 min, admin 2-hour timeout |
| **Security Headers** | XSS, clickjacking, MIME sniffing protection |
| **reCAPTCHA v3** | Invisible bot detection on booking and login forms |
| **Password Hashing** | bcrypt with cost factor 10 |

---

## 📧 Email System

The system uses **PHPMailer 7.0** with Gmail SMTP.

| Email Type | Trigger | Recipient |
|-----------|---------|-----------|
| Booking Confirmation | New booking submitted | Customer |
| Admin Notification | New booking received | Admin |
| Cancellation Notice | Booking cancelled | Customer |
| Booking Reminder | Daily cron at 8:00 AM | Customers with appt. tomorrow |

### Setting Up Daily Reminders (Windows)
1. Open **Task Scheduler** (Windows Search)
2. **Create Basic Task**
3. Name: `BG Barbershop Reminders`
4. Trigger: **Daily** at **8:00 AM**
5. Action: Start program → `C:\xampp\htdocs\bg-barbershop\cron\run_reminders.bat`

---

## 🖥️ Admin Panel

| Page | URL | Description |
|------|-----|-------------|
| Dashboard | `/views/admin/dashboard.php` | Overview and today's schedule |
| Appointments | `/views/admin/appointments.php` | Manage all bookings |
| Services | `/views/admin/services.php` | Manage services and pricing |
| Barbers | `/views/admin/barbers.php` | Manage staff profiles |
| Gallery | `/views/admin/gallery.php` | Manage photos |
| Reports | `/views/admin/reports.php` | Analytics and revenue |
| Block Dates | `/views/admin/blocked_dates.php` | Manage holidays and breaks |
| Walk-in Queue | `/views/admin/queue.php` | Real-time queue management |
| Reviews | `/views/admin/reviews.php` | Manage testimonials |
| Customers | `/views/admin/customers.php` | View registered customers |
| Settings | `/views/admin/settings.php` | System configuration |

---

## 📊 Database

**Database Name:** `biglang_gwapo`

| Table | Description |
|-------|-------------|
| `admins` | Admin panel users |
| `customers` | Registered customers |
| `barbers` | Barber profiles |
| `service_categories` | Service groupings |
| `services` | Individual services with pricing |
| `appointments` | All bookings |
| `appointment_services` | Services per booking (many-to-many) |
| `blocked_slots` | Blocked dates and time ranges |
| `walk_in_queue` | Walk-in customer queue |
| `gallery` | Gallery photos |
| `testimonials` | Customer reviews |
| `settings` | System-wide settings |
| `schedules` | Barber weekly schedules |

---

## 🎨 Design

- **Colors:** Black `#0D0D0D`, Gold `#C9A84C`, White `#FFFFFF`
- **Fonts:** Playfair Display (headings), Raleway (body)
- **Framework:** Bootstrap 5.3

---

## 📝 License

This project is proprietary software developed for **BG Biglang Gwapo Barbershop**.
All rights reserved © 2026.

---

<div align="center">
  <strong>Built with ❤️ for BG Biglang Gwapo Barbershop — Quezon City</strong>
</div>
