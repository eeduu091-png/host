<?php
/**
 * Search Employee API
 * Search for employees by first 5 digits of ID
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Employee.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }

    $digits = $_GET['digits'] ?? '';

    if (strlen($digits) < 1) {
        echo json_encode(['success' => true, 'employees' => []]);
        exit;
    }

    $employee = new Employee();
    $results = $employee->searchByDigits($digits);

    echo json_encode([
        'success' => true,
        'employees' => $results
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
