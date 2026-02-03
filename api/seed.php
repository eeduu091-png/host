<?php
/**
 * Seed Database with Admin Accounts
 * Run this file to create default admin users
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Database.php';

try {
    $db = Database::getInstance()->getConnection();

    // Default admin accounts
    $admins = [
        [
            'email' => 'greencorairtime@gmail.com',
            'password' => 'Admin@123',
            'name' => 'GreenCor Airtime',
            'role' => 'super_admin'
        ],
        [
            'email' => 'Gatutunewton1@gmail.com',
            'password' => 'Admin@123',
            'name' => 'Gatutunewton',
            'role' => 'admin'
        ]
    ];

    $createdAdmins = [];

    foreach ($admins as $admin) {
        // Check if admin exists
        $sql = "SELECT id FROM Admin WHERE email = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$admin['email']]);
        $existing = $stmt->fetch();

        if (!$existing) {
            // Create new admin
            $adminId = sprintf(
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

            $hashedPassword = password_hash($admin['password'], PASSWORD_BCRYPT);

            $sql = "INSERT INTO Admin (id, email, password, name, role, isActive, createdAt)
                    VALUES (?, ?, ?, ?, ?, 1, NOW())";
            $stmt = $db->prepare($sql);
            $stmt->execute([$adminId, $admin['email'], $hashedPassword, $admin['name'], $admin['role']]);

            $createdAdmins[] = [
                'email' => $admin['email'],
                'name' => $admin['name'],
                'role' => $admin['role']
            ];

            echo "✓ Created admin: " . $admin['email'] . "\n";
        } else {
            echo "⊘ Admin already exists: " . $admin['email'] . "\n";
            $createdAdmins[] = [
                'email' => $admin['email'],
                'name' => $admin['name'],
                'role' => $admin['role']
            ];
        }
    }

    // Return JSON response if accessed via HTTP
    if (isset($_SERVER['HTTP_HOST'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Database seeded successfully',
            'admins' => $createdAdmins
        ]);
    } else {
        echo "\n========================================\n";
        echo "Database seeding completed successfully!\n";
        echo "========================================\n";
        echo "\nAdmin Accounts:\n";
        foreach ($createdAdmins as $admin) {
            echo "  - " . $admin['name'] . " (" . $admin['email'] . ")\n";
        }
        echo "\nDefault password: Admin@123\n";
        echo "Please change this password after first login.\n";
        echo "\n========================================\n";
    }

} catch (Exception $e) {
    if (isset($_SERVER['HTTP_HOST'])) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
