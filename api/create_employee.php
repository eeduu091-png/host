<?php
/**
 * Create Employee API
 * Create a new employee
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Employee.php';

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

    // Validate required fields
    $required = ['employeeId', 'firstName', 'lastName', 'phone', 'role', 'region'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Field '$field' is required"]);
            exit;
        }
    }

    $employee = new Employee();

    // Check if employee ID already exists
    if ($employee->employeeIdExists($input['employeeId'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Employee ID already exists']);
        exit;
    }

    // Create employee
    $employeeId = $employee->create($input);

    echo json_encode([
        'success' => true,
        'employeeId' => $employeeId,
        'message' => 'Employee created successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
