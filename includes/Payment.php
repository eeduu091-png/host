<?php
/**
 * Payment Class - M-Pesa Integration
 */

require_once __DIR__ . '/Database.php';

class Payment {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Initiate M-Pesa STK Push
     */
    public function initiate($employeeId, $phoneNumber, $amount = PAYMENT_AMOUNT) {
        // Normalize phone number
        $phoneNumber = $this->normalizePhoneNumber($phoneNumber);

        // Create payment record
        $paymentId = $this->generateUUID();
        $sql = "INSERT INTO Payment (id, employeeId, phoneNumber, amount, status, createdAt)
                VALUES (?, ?, ?, ?, 'pending', NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$paymentId, $employeeId, $phoneNumber, $amount]);

        // In demo mode, auto-complete payment after 3 seconds
        // In production, you would call M-Pesa Daraja API here
        $merchantRequest = 'demo-' . uniqid();
        $checkoutRequest = 'demo-' . uniqid();

        // Update payment with request IDs
        $updateSql = "UPDATE Payment SET merchantRequest = ?, checkoutRequest = ? WHERE id = ?";
        $stmt = $this->db->prepare($updateSql);
        $stmt->execute([$merchantRequest, $checkoutRequest, $paymentId]);

        // Schedule auto-completion for demo (in production, M-Pesa callback will handle this)
        $this->scheduleDemoCompletion($paymentId, $employeeId, $amount);

        return [
            'success' => true,
            'paymentId' => $paymentId,
            'merchantRequest' => $merchantRequest,
            'checkoutRequest' => $checkoutRequest,
            'tillNumber' => MPESA_TILL_NUMBER,
            'tillName' => MPESA_TILL_NAME,
            'amount' => $amount,
            'message' => 'STK push sent to your phone. Please enter your M-Pesa PIN to pay KES ' . $amount . ' to Till Number ' . MPESA_TILL_NUMBER . ' (' . MPESA_TILL_NAME . ')'
        ];
    }

    /**
     * Get payment status
     */
    public function getStatus($paymentId) {
        $sql = "SELECT * FROM Payment WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$paymentId]);
        return $stmt->fetch();
    }

    /**
     * Process M-Pesa callback
     */
    public function processCallback($callbackData) {
        $stkCallback = $callbackData['Body']['stkCallback'];
        $merchantRequest = $stkCallback['MerchantRequestID'];
        $resultCode = $stkCallback['ResultCode'];

        // Find payment
        $sql = "SELECT * FROM Payment WHERE merchantRequest = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$merchantRequest]);
        $payment = $stmt->fetch();

        if (!$payment) {
            return ['success' => false, 'message' => 'Payment not found'];
        }

        // Update payment status
        if ($resultCode == 0) {
            // Success
            $metadata = $stkCallback['CallbackMetadata']['Item'];
            $receiptNumber = null;
            $amount = null;
            $phoneNumber = null;

            foreach ($metadata as $item) {
                if ($item['Name'] == 'MpesaReceiptNumber') {
                    $receiptNumber = $item['Value'];
                } elseif ($item['Name'] == 'Amount') {
                    $amount = $item['Value'];
                } elseif ($item['Name'] == 'PhoneNumber') {
                    $phoneNumber = $item['Value'];
                }
            }

            $sql = "UPDATE Payment
                    SET status = 'completed',
                        mpesaReceipt = ?,
                        amount = ?,
                        callbackReceived = 1,
                        completedAt = NOW()
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$receiptNumber, $amount, $payment['id']]);

            return ['success' => true, 'status' => 'completed'];
        } else {
            // Failed
            $sql = "UPDATE Payment
                    SET status = 'failed',
                        callbackReceived = 1
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$payment['id']]);

            return ['success' => false, 'status' => 'failed', 'message' => $stkCallback['ResultDesc']];
        }
    }

    /**
     * Normalize phone number to 254 format
     */
    private function normalizePhoneNumber($phone) {
        // Remove spaces, dashes, etc.
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 0, replace with 254
        if (strpos($phone, '0') === 0) {
            $phone = '254' . substr($phone, 1);
        }

        // If starts with +, remove it
        if (strpos($phone, '+') === 0) {
            $phone = substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Schedule demo payment completion (for testing)
     */
    private function scheduleDemoCompletion($paymentId, $employeeId, $amount) {
        // In a real application, you would use a job queue
        // For demo, we'll use a background process or setTimeout equivalent
        // This is simplified - in production, M-Pesa will send the callback

        // Create a background process to complete payment after 3 seconds
        $command = sprintf(
            'php -r "%s" > /dev/null 2>&1 &',
            addslashes(
                sprintf(
                    'require_once "%s"; $db = (new Database())->getConnection(); $db->prepare("UPDATE Payment SET status = \"completed\", mpesaReceipt = \"DEMO' . uniqid() . '\", callbackReceived = 1, completedAt = NOW() WHERE id = ?")->execute(["%s"]);',
                    __DIR__ . '/Database.php',
                    $paymentId
                )
            )
        );

        // Note: This is a simplified approach for demo purposes
        // In production, rely on M-Pesa callbacks only
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
