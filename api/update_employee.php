<?php
/**
 * Update Employee API
 * Update an existing employee
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
    $employeeId = $input['id'] ?? '';

    if (empty($employeeId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Employee ID required']);
        exit;
    }

    $employee = new Employee();

    // Build update data (exclude id and readonly fields)
    $updateData = [];
    $allowedFields = ['employeeId', 'firstName', 'lastName', 'email', 'phone', 'role', 'region', 'department', 'site', 'contractType', 'salary'];

    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updateData[$field] = $input[$field];
        }
    }

    if (empty($updateData)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'No fields to update']);
        exit;
    }

    $result = $employee->update($employeeId, $updateData);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Employee updated successfully'
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Employee not found']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
