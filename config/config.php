<?php
/**
 * Configuration File for Maayash Communications ID Card System
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'maayash_id_system');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// App Configuration
define('APP_NAME', 'Maayash Communications');
define('COMPANY_NAME', 'Maayash Communications');
define('CONTRACTOR_FOR', 'Safaricom');
define('APP_URL', 'http://localhost:8000');

// Safaricom Branding Colors
define('SAFARICOM_GREEN', '#009933');
define('SAFARICOM_RED', '#E60000');
define('SAFARICOM_DARK', '#006622');

// Payment Configuration
define('PAYMENT_AMOUNT', 50.00);
define('CURRENCY', 'KES');

// M-Pesa Till Number Configuration
define('MPESA_TILL_NUMBER', '6604923');
define('MPESA_TILL_NAME', 'BUY GOODS GREEN COLOR NETWORKS');

// M-Pesa Daraja API Configuration (Sandbox)
define('MPESA_ENVIRONMENT', 'sandbox');
define('MPESA_CONSUMER_KEY', 'your_consumer_key_here');
define('MPESA_CONSUMER_SECRET', 'your_consumer_secret_here');
define('MPESA_PASSKEY', 'your_passkey_here');
define('MPESA_SHORTCODE', '174379');
define('MPESA_CALLBACK_URL', APP_URL . '/api/mpesa_callback.php');

// File Upload Configuration
define('UPLOAD_MAX_SIZE', 5242880); // 5MB in bytes
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/jpg']);
define('TEMP_UPLOAD_DIR', __DIR__ . '/../uploads/temp/');
define('ID_CARD_DIR', __DIR__ . '/../uploads/id_cards/');

// Session Configuration
define('SESSION_NAME', 'MAAYASH_SESSION');
define('SESSION_LIFETIME', 3600); // 1 hour

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Africa/Nairobi');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}
