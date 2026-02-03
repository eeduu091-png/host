<?php
/**
 * Upload Photo API
 * Handle employee photo uploads
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/config.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }

    if (!isset($_FILES['photo'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'No photo uploaded']);
        exit;
    }

    $file = $_FILES['photo'];

    // Validate file size
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'File too large. Maximum size is 5MB']);
        exit;
    }

    // Validate file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid file type. Only JPEG and PNG allowed']);
        exit;
    }

    // Read file and convert to base64
    $imageData = file_get_contents($file['tmp_name']);
    $base64Data = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);

    echo json_encode([
        'success' => true,
        'photoData' => $base64Data
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
