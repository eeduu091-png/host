<?php
/**
 * DownloadCode Class
 */

require_once __DIR__ . '/Database.php';

class DownloadCode {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Generate a new download code
     */
    public function create($adminId, $maxUses = 1, $expiresAt = null) {
        do {
            $code = $this->generateCode();
        } while ($this->codeExists($code));

        $sql = "INSERT INTO DownloadCode (id, code, maxUses, usedCount, expiresAt, createdBy, createdAt)
                VALUES (?, ?, ?, 0, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $this->generateUUID(),
            $code,
            $maxUses,
            $expiresAt
        ]);

        return [
            'id' => $this->db->lastInsertId(),
            'code' => $code,
            'maxUses' => $maxUses,
            'expiresAt' => $expiresAt
        ];
    }

    /**
     * Validate a download code
     */
    public function validate($code, $employeeId) {
        $sql = "SELECT * FROM DownloadCode
                WHERE code = ? AND isActive = 1
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$code]);
        $codeData = $stmt->fetch();

        if (!$codeData) {
            return ['valid' => false, 'message' => 'Invalid download code'];
        }

        // Check if expired
        if ($codeData['expiresAt'] && strtotime($codeData['expiresAt']) < time()) {
            return ['valid' => false, 'message' => 'Download code has expired'];
        }

        // Check if uses remaining
        if ($codeData['usedCount'] >= $codeData['maxUses']) {
            return ['valid' => false, 'message' => 'Download code has been fully used'];
        }

        // Increment used count
        $this->incrementUsedCount($codeData['id']);

        return ['valid' => true, 'codeId' => $codeData['id']];
    }

    /**
     * Get all download codes
     */
    public function getAll() {
        $sql = "SELECT dc.*, a.name as createdBy
                FROM DownloadCode dc
                LEFT JOIN Admin a ON dc.createdBy = a.id
                ORDER BY dc.createdAt DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Deactivate a download code
     */
    public function deactivate($id) {
        $sql = "UPDATE DownloadCode SET isActive = 0 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Reactivate a download code (reset used count)
     */
    public function reactivate($id, $newMaxUses = null) {
        $sql = "UPDATE DownloadCode SET isActive = 1, usedCount = 0";
        $params = [];

        if ($newMaxUses !== null) {
            $sql .= ", maxUses = ?";
            $params[] = $newMaxUses;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete a download code
     */
    public function delete($id) {
        $sql = "DELETE FROM DownloadCode WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Check if code exists
     */
    private function codeExists($code) {
        $sql = "SELECT COUNT(*) as count FROM DownloadCode WHERE code = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$code]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Increment used count
     */
    private function incrementUsedCount($id) {
        $sql = "UPDATE DownloadCode SET usedCount = usedCount + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Generate random 8-character code
     */
    private function generateCode() {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < 8; $i++) {
            if ($i == 2 || $i == 5) {
                $code .= '-';
            } else {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
        }
        return $code;
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
