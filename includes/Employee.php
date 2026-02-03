<?php
/**
 * Employee Class
 */

require_once __DIR__ . '/Database.php';

class Employee {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Search employees by first 5 digits of ID
     */
    public function searchByDigits($digits) {
        $sql = "SELECT id, employeeId, firstName, lastName, phone, role, region, department, site
                FROM Employee
                WHERE employeeId LIKE ? AND isActive = 1
                LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$digits . '%']);
        return $stmt->fetchAll();
    }

    /**
     * Get employee by ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM Employee WHERE id = ? AND isActive = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get employee by employeeId
     */
    public function getByEmployeeId($employeeId) {
        $sql = "SELECT * FROM Employee WHERE employeeId = ? AND isActive = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$employeeId]);
        return $stmt->fetch();
    }

    /**
     * Get all employees
     */
    public function getAll($limit = 20, $offset = 0) {
        $sql = "SELECT e.*,
                (SELECT COUNT(*) FROM DownloadHistory WHERE employeeId = e.employeeId) as downloadCount
                FROM Employee e
                WHERE e.isActive = 1
                ORDER BY e.createdAt DESC
                LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Get total employee count
     */
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as count FROM Employee WHERE isActive = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    /**
     * Create new employee
     */
    public function create($data) {
        $sql = "INSERT INTO Employee (id, employeeId, firstName, lastName, email, phone, role, region, department, site, contractType, salary, createdAt)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $this->generateUUID(),
            $data['employeeId'],
            $data['firstName'],
            $data['lastName'],
            $data['email'] ?? null,
            $data['phone'],
            $data['role'],
            $data['region'],
            $data['department'] ?? null,
            $data['site'] ?? null,
            $data['contractType'] ?? 'permanent',
            $data['salary'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Update employee
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            if ($key !== 'id' && $key !== 'createdAt') {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE Employee SET " . implode(', ', $fields) . ", updatedAt = NOW() WHERE id = ?";
        $values[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Soft delete employee
     */
    public function delete($id) {
        $sql = "UPDATE Employee SET isActive = 0, updatedAt = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Check if employee ID exists
     */
    public function employeeIdExists($employeeId) {
        $sql = "SELECT COUNT(*) as count FROM Employee WHERE employeeId = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$employeeId]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Import employees from array
     */
    public function importBatch($employees) {
        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($employees as $index => $emp) {
            try {
                // Skip if employee ID already exists
                if ($this->employeeIdExists($emp['employeeId'])) {
                    $skipped++;
                    continue;
                }

                $this->create($emp);
                $imported++;
            } catch (Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors
        ];
    }

    /**
     * Generate UUID
     */
    private function generateUUID() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
