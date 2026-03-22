# 💈 BG Biglang Gwapo Barbershop — Management System

<div align="center">

**A full-stack web-based barbershop management and online booking system**

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![PHPMailer](https://img.shields.io/badge/PHPMailer-7.0-EA4335?style=flat-square&logo=gmail&logoColor=white)

</div>

---

## 📋 Overview

**BG Biglang Gwapo Barbershop** is a complete web-based management system for a barbershop business in Quezon City, Metro Manila. It covers online booking, customer management, walk-in queue, email notifications, and reporting.

---

## ✨ Features

### 👤 Customer Side
- 🗓️ Online appointment booking
- 📧 Automatic email confirmation
- 🖨️ Printable booking confirmation
- 👤 Customer accounts and booking history
- 📱 Mobile responsive design
- 🖼️ Photo gallery with lightbox
- ⭐ Customer reviews and testimonials

### 🔧 Admin Side
- 📊 Dashboard with daily schedule
- 📅 Appointment management
- ✂️ Services and pricing management
- 💈 Staff management
- 📈 Reports and analytics
- 🚫 Block dates for holidays and breaks
- 🏪 Walk-in queue management
- ⭐ Reviews management
- 👥 Customer database
- ⚙️ System settings

### 📧 Emails
- Booking confirmation
- Admin notification
- Cancellation notice
- Daily appointment reminders

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
| Server   | Apache / XAMPP |

---

## 🚀 Installation

### Prerequisites
- XAMPP with PHP 8.2+ and MySQL
- Composer
- Internet connection

### Steps

**1. Copy project to XAMPP**
```bash
# Place the project folder inside your htdocs directory
```

**2. Set up the database**
```
1. Open phpMyAdmin
2. Create a new database
3. Import the provided .sql file
```

**3. Install dependencies**
```bash
composer install
```

**4. Configure the application**
```bash
# Copy the example config files and fill in your own credentials
# See Configuration section below
```

**5. Access the system**
```
Visit the project URL in your browser
```

---

## ⚙️ Configuration

All sensitive configuration is stored in the `config/` directory.
**These files are excluded from this repository for security.**

You will need to create and configure the following files manually:

| File | Purpose |
|------|---------|
| `config/db.php` | Database connection credentials |
| `config/mail.php` | Gmail SMTP credentials |
| `config/security.php` | reCAPTCHA keys and security settings |

> ⚠️ **Never commit these files to any public repository.**

---

## 🔒 Security

This system implements multiple layers of security:

- CSRF token protection on all forms
- Honeypot fields for bot detection
- Brute force protection with account lockout
- Rate limiting on booking and login endpoints
- Input sanitization on all user inputs
- Session security with auto-timeout
- Security headers (XSS, clickjacking, MIME sniffing)
- reCAPTCHA v3 invisible protection
- Password hashing with bcrypt

---

## 📊 Database

The system uses a MySQL database with 13 tables covering appointments, customers, barbers, services, gallery, reviews, settings, and more.

> The database schema file (`*.sql`) is included separately and provided to authorized users only.

---

## 🎨 Design

- **Theme:** Black, Gold, and White
- **Fonts:** Playfair Display, Raleway
- **Framework:** Bootstrap 5.3

---

## ⚠️ Important Notes

- Default credentials are provided separately to authorized personnel only
- All configuration files must be set up manually — they are not included in this repository
- This system is intended for use on a secured server environment
- HTTPS/SSL is strongly recommended for production deployment

---

## 📝 License

This project is proprietary software developed for **BG Biglang Gwapo Barbershop**.
Unauthorized use, copying, or distribution is strictly prohibited.
All rights reserved © 2026.

---

<div align="center">
  <strong>Built for BG Biglang Gwapo Barbershop — Quezon City 💈</strong>
</div>