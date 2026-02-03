<?php
/**
 * Deactivate Download Code API
 * Deactivate a download code
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

    $input = json_decode(file_get_contents('php://input'), true);
    $codeId = $input['codeId'] ?? '';

    if (empty($codeId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Code ID required']);
        exit;
    }

    $downloadCode = new DownloadCode();
    $result = $downloadCode->deactivate($codeId);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Code deactivated successfully'
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Code not found']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
