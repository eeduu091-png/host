<?php
/**
 * Delete Employee API
 * Delete (soft delete) an employee
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Employee.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }

    // Require admin login
    $auth = new Auth();
    $auth->requireAdminLogin();

    $employeeId = $_GET['id'] ?? '';

    if (empty($employeeId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Employee ID required']);
        exit;
    }

    $employee = new Employee();
    $result = $employee->delete($employeeId);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Employee deleted successfully'
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Employee not found']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
