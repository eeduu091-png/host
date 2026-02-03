<?php
/**
 * Generate ID Card API
 * Generate ID card for employee
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/IDCard.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    $employeeId = $input['employeeId'] ?? '';
    $photoData = $input['photoData'] ?? '';
    $downloadType = $input['downloadType'] ?? '';
    $paymentId = $input['paymentId'] ?? null;
    $downloadCodeId = $input['downloadCodeId'] ?? null;

    if (empty($employeeId) || empty($photoData) || empty($downloadType)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }

    $idCard = new IDCard();
    $result = $idCard->generate($employeeId, $photoData, $downloadType, $paymentId, $downloadCodeId);

    echo json_encode($result);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
