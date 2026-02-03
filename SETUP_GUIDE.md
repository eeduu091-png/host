# Quick Setup Guide

Get your Maayash Communications ID Card System running in 15 minutes!

## Prerequisites Check

Before starting, ensure you have:

- [ ] PHP 7.4 or higher installed (`php -v`)
- [ ] MySQL 5.7 or higher installed (`mysql --version`)
- [ ] Apache or Nginx web server
- [ ] Access to MySQL with root or admin privileges
- [ ] Text editor (VS Code, Sublime Text, etc.)

---

## Step 1: Setup Database (2 minutes)

### 1.1 Create Database

Open terminal/command prompt and run:

```bash
mysql -u root -p
```

Enter your MySQL password when prompted.

### 1.2 Run Schema

In MySQL console:

```sql
CREATE DATABASE maayash_id_system;
USE maayash_id_system;
SOURCE /path/to/your/project/database/schema.sql;
EXIT;
```

Or run directly from command line:

```bash
mysql -u root -p < database/schema.sql
```

**Expected output:** No errors, tables created successfully.

---

## Step 2: Configure Database Connection (1 minute)

### 2.1 Edit Config File

Open `config/config.php` in your text editor.

### 2.2 Update Database Credentials

Find these lines and update:

```php
define('DB_HOST', 'localhost');        // Usually localhost
define('DB_NAME', 'maayash_id_system'); // Created in Step 1
define('DB_USER', 'root');              // Your MySQL username
define('DB_PASS', '');                  // Your MySQL password
```

Save the file.

---

## Step 3: Create Admin Accounts (1 minute)

### 3.1 Run Seed Script

In terminal, navigate to project directory and run:

```bash
php api/seed.php
```

**Expected output:**

```
✓ Created admin: greencorairtime@gmail.com
✓ Created admin: Gatutunewton1@gmail.com

========================================
Database seeding completed successfully!
========================================

Admin Accounts:
  - GreenCor Airtime (greencorairtime@gmail.com)
  - Gatutunewton (Gatutunewton1@gmail.com)

Default password: Admin@123
Please change this password after first login.

========================================
```

### 3.2 Verify Admin Accounts

```bash
mysql -u root -p maayash_id_system
```

```sql
SELECT email, name FROM Admin;
```

**Expected output:**

```
+---------------------------+-------------------+
| email                     | name              |
+---------------------------+-------------------+
| greencorairtime@gmail.com | GreenCor Airtime  |
| Gatutunewton1@gmail.com   | Gatutunewton      |
+---------------------------+-------------------+
```

---

## Step 4: Set Up Web Server (5 minutes)

### Option A: Apache (Recommended)

#### 4.1 Enable mod_rewrite

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### 4.2 Configure Virtual Host

Create `/etc/apache2/sites-available/id-card-system.conf`:

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/your/project/public

    <Directory /path/to/your/project/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/id-card-system-error.log
    CustomLog ${APACHE_LOG_DIR}/id-card-system-access.log combined
</VirtualHost>
```

#### 4.3 Enable Site

```bash
sudo a2ensite id-card-system.conf
sudo systemctl reload apache2
```

### Option B: Nginx

Create `/etc/nginx/sites-available/id-card-system`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/your/project/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/id-card-system /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Option C: PHP Built-in Server (Development Only)

```bash
cd public
php -S localhost:8000
```

Access at: `http://localhost:8000`

⚠️ **Warning:** Built-in server is for development only. Use Apache or Nginx for production.

---

## Step 5: Set File Permissions (1 minute)

```bash
# Make directories writable
chmod 755 public
chmod 755 public/css
chmod 755 public/js
chmod 755 public/admin
chmod 755 uploads
chmod 755 uploads/temp
chmod 755 uploads/id_cards

# Make config and includes readable (but not writable by web)
chmod 644 config/config.php
chmod 644 includes/*.php
```

---

## Step 6: Test the System (5 minutes)

### 6.1 Access Employee Portal

Open browser and visit: `http://your-domain.com/` or `http://localhost:8000`

**Expected:**
- Green header with "MAAYASH COMMUNICATIONS"
- 5-step progress indicator
- Search form for ID digits

### 6.2 Access Admin Panel

Visit: `http://your-domain.com/admin/` or `http://localhost:8000/admin/`

**Expected:**
- Login form
- Enter: `greencorairtime@gmail.com` / `Admin@123`
- Redirect to dashboard

### 6.3 Test Admin Functions

1. **Generate Download Code:**
   - Click "Download Codes" tab
   - Click "+ Generate Code"
   - Set max uses to 1
   - Click "Generate"
   - Copy the code (e.g., AB12-CD34)

2. **Add Employee (Optional):**
   - Click "Employees" tab
   - Click "+ Add Employee"
   - Fill in details
   - Click "Add Employee"

### 6.4 Test Employee Flow

1. Go back to employee portal
2. Enter first 5 digits of employee ID (if you have one in database)
3. Select profile
4. Upload a photo
5. Choose "Download Code" option
6. Enter the code you generated
7. Download the ID card

**Expected:**
- Professional ID card with Safaricom branding
- Employee photo displayed
- QR code included
- PNG file downloads successfully

---

## Troubleshooting

### Database Connection Error

**Error:** "Database connection failed"

**Solution:**
1. Check MySQL is running: `sudo systemctl status mysql`
2. Verify credentials in `config/config.php`
3. Ensure database exists: `SHOW DATABASES;`
4. Check MySQL user permissions

### 404 Not Found

**Error:** Page not found

**Solution:**
1. Check web server configuration
2. Ensure document root points to `public/` directory
3. Check file permissions
4. For Apache: ensure mod_rewrite is enabled

### Permission Denied

**Error:** "Permission denied" when uploading

**Solution:**
```bash
chmod 755 uploads
chmod 755 uploads/temp
chmod 755 uploads/id_cards
chown -R www-data:www-data uploads  # For Apache
# or
chown -R nginx:nginx uploads  # For Nginx
```

### M-Pesa Not Working

**Error:** STK push fails

**Solution:**
1. Verify API credentials in `config/config.php`
2. Ensure callback URL is publicly accessible
3. Check M-Pesa Daraja dashboard
4. For testing, system auto-completes demo payments after 3 seconds

### Admin Login Fails

**Error:** "Invalid email or password"

**Solution:**
1. Run seed script again: `php api/seed.php`
2. Check database: `SELECT * FROM Admin;`
3. Verify email and password are correct
4. Clear browser cache and cookies

---

## Production Deployment Checklist

Before going live, ensure:

- [ ] Change default admin passwords
- [ ] Update M-Pesa credentials to production
- [ ] Enable HTTPS (SSL certificate)
- [ ] Set up database backups
- [ ] Configure error logging
- [ ] Set up monitoring
- [ ] Test all payment flows with small amounts
- [ ] Disable error display in production
- [ ] Set strong file permissions
- [ ] Configure firewall rules
- [ ] Set up email notifications (if needed)
- [ ] Test on mobile devices
- [ ] Load test with multiple users

---

## Next Steps

1. **Import Your Employees:**
   - Prepare Excel file with employee data
   - Use import script or add manually via admin panel

2. **Configure M-Pesa:**
   - Register at https://developer.safaricom.co.ke/
   - Get production credentials
   - Update `config/config.php`

3. **Customize Branding:**
   - Update company name in `config/config.php`
   - Modify colors in `public/css/style.css`
   - Add company logo to `public/images/`

4. **Train Users:**
   - Show employees how to use the portal
   - Train admins on dashboard usage
   - Provide support contact information

---

## Support Resources

- 📖 Full Documentation: See `README.md`
- 🐛 Report Issues: Contact support
- 💬 Get Help: Email support@maayashcommunications.com

---

## Success Indicators

You'll know everything is working when:

✅ Database tables created successfully
✅ Admin accounts created via seed script
✅ Can login to admin panel
✅ Can generate download codes
✅ Employee portal loads correctly
✅ Can search for employees
✅ Can upload photos
✅ ID cards generate and download
✅ All pages are responsive on mobile

---

**Estimated Setup Time:** 15 minutes
**Difficulty:** Beginner to Intermediate

Good luck! 🚀
