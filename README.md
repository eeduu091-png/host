# Maayash Communications ID Card Management System

A professional employee ID card generation and management system for Maayash Communications, a Safaricom contractor. Built with PHP, MySQL, HTML, CSS, and JavaScript.

## Features

### Employee Portal
- 🔍 Search for employee profile by first 5 digits of ID
- ✅ Verify and confirm employee details
- 📷 Upload passport photo for ID card
- 💳 Pay KES 50 via M-Pesa STK Push
- 🎟️ Or use admin-generated download code
- 🪪 Generate and download professional ID card with QR code
- 📱 Fully responsive design

### Admin Dashboard
- 👤 Manage employees (add, view, delete)
- 🎟️ Generate download codes with usage limits
- 📊 View statistics and analytics
- 📝 Activity logging
- 🔐 Secure authentication

### ID Card Features
- Professional Safaricom-branded design
- High-resolution (1200x760px)
- QR code containing employee data
- Employee photo
- All relevant details (name, ID, phone, role, region, etc.)
- Download as PNG

## Technology Stack

- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Payment:** M-Pesa Daraja API (STK Push)
- **No frameworks** - Pure PHP/JS for maximum efficiency

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache or Nginx web server
- mod_php or PHP-FPM

### Step 1: Clone/Download Files

Place all files in your web server directory (e.g., `/var/www/html/id-card-system/`).

### Step 2: Create Database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE maayash_id_system;
USE maayash_id_system;
SOURCE database/schema.sql;
EXIT;
```

### Step 3: Configure Database

Edit `config/config.php` and update database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'maayash_id_system');
define('DB_USER', 'your_mysql_user');
define('DB_PASS', 'your_mysql_password');
```

### Step 4: Seed Admin Accounts

Run the seed script to create default admin accounts:

```bash
php api/seed.php
```

Or visit: `http://your-domain.com/api/seed.php`

### Step 5: Set File Permissions

```bash
chmod 755 public
chmod 755 public/css
chmod 755 public/js
chmod 755 public/admin
chmod 755 uploads
chmod 755 uploads/temp
chmod 755 uploads/id_cards
```

### Step 6: Configure Web Server

#### Apache (.htaccess included)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ public/$1 [L]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /public/$1.php?$query_string;
}
```

### Step 7: Access the Application

- **Employee Portal:** `http://your-domain.com/`
- **Admin Panel:** `http://your-domain.com/admin/`

## Default Admin Credentials

⚠️ **IMPORTANT:** Change these passwords after first login!

| Email | Password | Name |
|-------|----------|------|
| greencorairtime@gmail.com | Admin@123 | GreenCor Airtime |
| Gatutunewton1@gmail.com | Admin@123 | Gatutunewton |

## File Structure

```
/
├── config/
│   └── config.php          # Configuration file
├── database/
│   └── schema.sql          # Database schema
├── includes/
│   ├── Database.php        # Database connection class
│   ├── Auth.php            # Authentication class
│   ├── Employee.php        # Employee management
│   ├── Payment.php         # M-Pesa integration
│   ├── DownloadCode.php    # Download code management
│   └── IDCard.php          # ID card generation
├── api/
│   ├── seed.php            # Seed admin accounts
│   ├── search_employee.php # Search employees
│   ├── upload_photo.php    # Handle photo uploads
│   ├── initiate_payment.php # Initiate M-Pesa payment
│   ├── check_payment.php   # Check payment status
│   ├── validate_code.php   # Validate download code
│   ├── generate_id.php     # Generate ID card
│   ├── admin_login.php     # Admin authentication
│   ├── get_employees.php   # Get all employees
│   ├── create_employee.php # Create employee
│   ├── delete_employee.php # Delete employee
│   ├── get_codes.php       # Get download codes
│   ├── create_code.php     # Generate code
│   ├── deactivate_code.php # Deactivate code
│   └── delete_code.php     # Delete code
├── public/
│   ├── index.php           # Employee portal
│   ├── css/
│   │   └── style.css       # Main stylesheet
│   ├── js/
│   │   ├── script.js       # Employee portal JS
│   │   └── admin.js        # Admin dashboard JS
│   └── admin/
│       ├── login.php       # Admin login
│       └── dashboard.php   # Admin dashboard
├── uploads/
│   ├── temp/               # Temporary uploads
│   └── id_cards/           # Generated ID cards
└── README.md               # This file
```

## M-Pesa Configuration

To enable M-Pesa payments, update `config/config.php` with your Daraja API credentials:

```php
define('MPESA_CONSUMER_KEY', 'your_consumer_key');
define('MPESA_CONSUMER_SECRET', 'your_consumer_secret');
define('MPESA_PASSKEY', 'your_passkey');
define('MPESA_SHORTCODE', '174379');
define('MPESA_CALLBACK_URL', 'https://your-domain.com/api/mpesa_callback.php');
```

Get credentials from: https://developer.safaricom.co.ke/

## Usage

### For Employees

1. Visit the employee portal
2. Enter first 5 digits of ID number
3. Select your profile from dropdown
4. Verify your details
5. Upload a passport photo
6. Choose payment method:
   - **M-Pesa:** Enter phone number, pay KES 50
   - **Download Code:** Enter admin-provided code
7. Download your ID card

### For Admins

1. Login to admin panel
2. **Manage Employees:**
   - View all employees
   - Add new employees
   - Delete employees
3. **Manage Download Codes:**
   - Generate new codes
   - Set usage limits
   - Set expiration dates
   - Deactivate/delete codes
4. **View Statistics:**
   - Total employees
   - Downloads today
   - Active codes
   - Revenue

## Security Features

- ✅ Password hashing with bcrypt
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS protection
- ✅ Secure session management
- ✅ File upload validation
- ✅ Admin authentication
- ✅ Input validation and sanitization

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Support

For issues or questions, contact:
- Email: support@maayashcommunications.com
- Phone: [Your Phone Number]

## License

Proprietary - Maayash Communications © 2025

---

**Built with ❤️ for Maayash Communications**
