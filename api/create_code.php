<?php
/**
 * Create Download Code API
 * Generate a new download code
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/DownloadCode.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }

    // Require admin login
    $auth = new Auth();
    $auth->requireAdminLogin();

    $admin = $auth->getCurrentAdmin();
    if (!$admin) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    $maxUses = isset($input['maxUses']) ? (int)$input['maxUses'] : 1;
    $expiresAt = $input['expiresAt'] ?? null;

    $downloadCode = new DownloadCode();
    $result = $downloadCode->create($admin['id'], $maxUses, $expiresAt);

    echo json_encode([
        'success' => true,
        'code' => $result
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
