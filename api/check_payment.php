<?php
/**
 * Check Payment Status API
 * Check the status of a payment
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Payment.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }

    $paymentId = $_GET['payment_id'] ?? '';

    if (empty($paymentId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Payment ID required']);
        exit;
    }

    $payment = new Payment();
    $status = $payment->getStatus($paymentId);

    if (!$status) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Payment not found']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'status' => $status['status'],
        'completed' => $status['status'] === 'completed'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
