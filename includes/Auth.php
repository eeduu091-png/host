<?php
/**
 * Authentication Class
 */

require_once __DIR__ . '/Database.php';

class Auth {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Login admin
     */
    public function loginAdmin($email, $password) {
        $sql = "SELECT * FROM Admin WHERE email = ? AND isActive = 1 LIMIT 1";
        $admin = $this->db->prepare($sql);
        $admin->execute([$email]);
        $result = $admin->fetch();

        if ($result && password_verify($password, $result['password'])) {
            // Update last login
            $this->db->prepare("UPDATE Admin SET lastLoginAt = NOW() WHERE id = ?")
                ->execute([$result['id']]);

            // Set session
            $_SESSION['admin_id'] = $result['id'];
            $_SESSION['admin_email'] = $result['email'];
            $_SESSION['admin_name'] = $result['name'];
            $_SESSION['admin_role'] = $result['role'];

            // Log activity
            $this->logActivity($result['id'], 'login', 'Admin', $result['id'], 'Admin logged in');

            return $result;
        }

        return false;
    }

    /**
     * Check if admin is logged in
     */
    public function isAdminLoggedIn() {
        return isset($_SESSION['admin_id']);
    }

    /**
     * Get current admin
     */
    public function getCurrentAdmin() {
        if (!$this->isAdminLoggedIn()) {
            return null;
        }

        $sql = "SELECT id, email, name, role FROM Admin WHERE id = ? AND isActive = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$_SESSION['admin_id']]);
        return $stmt->fetch();
    }

    /**
     * Logout admin
     */
    public function logoutAdmin() {
        if ($this->isAdminLoggedIn()) {
            $this->logActivity($_SESSION['admin_id'], 'logout', 'Admin', $_SESSION['admin_id'], 'Admin logged out');
        }

        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_email']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_role']);
        session_destroy();
    }

    /**
     * Require admin login (redirects if not logged in)
     */
    public function requireAdminLogin() {
        if (!$this->isAdminLoggedIn()) {
            header('Location: /admin/login.php');
            exit;
        }
    }

    /**
     * Log admin activity
     */
    private function logActivity($adminId, $action, $entity = null, $entityId = null, $details = null) {
        $sql = "INSERT INTO ActivityLog (id, adminId, action, entity, entityId, details, ipAddress, createdAt)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $this->generateUUID(),
            $adminId,
            $action,
            $entity,
            $entityId,
            $details,
            $this->getClientIP()
        ]);
    }

    /**
     * Get client IP address
     */
    private function getClientIP() {
        $ip = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Generate UUID
     */
    private function generateUUID() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
