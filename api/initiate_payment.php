<?php
/**
 * Initiate Payment API
 * Initiate M-Pesa STK Push payment
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Payment.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    $employeeId = $input['employeeId'] ?? '';
    $phoneNumber = $input['phoneNumber'] ?? '';
    $amount = $input['amount'] ?? PAYMENT_AMOUNT;

    if (empty($employeeId) || empty($phoneNumber)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }

    $payment = new Payment();
    $result = $payment->initiate($employeeId, $phoneNumber, $amount);

    echo json_encode($result);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
