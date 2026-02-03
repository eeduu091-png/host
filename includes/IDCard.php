<?php
/**
 * IDCard Generator Class
 */

require_once __DIR__ . '/Database.php';

class IDCard {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Generate ID card for employee
     */
    public function generate($employeeId, $photoData, $downloadType, $paymentId = null, $downloadCodeId = null) {
        // Get employee data
        $sql = "SELECT * FROM Employee WHERE id = ? AND isActive = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$employeeId]);
        $employee = $stmt->fetch();

        if (!$employee) {
            return ['success' => false, 'message' => 'Employee not found'];
        }

        // Create canvas and draw ID card
        $imageData = $this->drawIDCard($employee, $photoData);

        // Log download
        $this->logDownload($employee['employeeId'], $downloadType, $paymentId, $downloadCodeId);

        // Save to file
        $filename = $this->saveIDCard($imageData, $employee['employeeId']);

        return [
            'success' => true,
            'imageData' => $imageData,
            'filename' => $filename
        ];
    }

    /**
     * Draw ID card on canvas
     */
    private function drawIDCard($employee, $photoData) {
        // Canvas dimensions
        $width = 1200;
        $height = 760;

        // Create image
        $image = imagecreatetruecolor($width, $height);

        // Colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $safaricomGreen = imagecolorallocate($image, 0, 153, 51);
        $safaricomRed = imagecolorallocate($image, 230, 0, 0);
        $black = imagecolorallocate($image, 0, 0, 0);
        $darkGray = imagecolorallocate($image, 51, 51, 51);
        $lightGray = imagecolorallocate($image, 240, 240, 240);

        // Fill white background
        imagefill($image, 0, 0, $white);

        // Draw green header (100px height)
        imagefilledrectangle($image, 0, 0, $width, 100, $safaricomGreen);

        // Add company name
        $companyName = 'MAAYASH COMMUNICATIONS';
        $fontSize = 36;
        $fontColor = $white;
        $this->drawCenteredText($image, $companyName, $fontSize, $fontColor, 35);

        // Add subtitle
        $subtitle = 'Contractor for: SAFARICOM';
        $fontSize = 20;
        $this->drawCenteredText($image, $subtitle, $fontSize, $fontColor, 75);

        // Load and draw employee photo
        $photoX = 50;
        $photoY = 120;
        $photoWidth = 200;
        $photoHeight = 250;

        if ($photoData) {
            $photoImage = $this->loadPhoto($photoData);
            if ($photoImage) {
                // Resize photo to fit
                $photoResized = imagecreatetruecolor($photoWidth, $photoHeight);
                imagecopyresampled(
                    $photoResized,
                    $photoImage,
                    0, 0, 0, 0,
                    $photoWidth, $photoHeight,
                    imagesx($photoImage),
                    imagesy($photoImage)
                );

                // Add rounded corners effect
                $this->addRoundedCorners($photoResized, $photoWidth, $photoHeight, 15);

                // Draw photo
                imagecopy($image, $photoResized, $photoX, $photoY, 0, 0, $photoWidth, $photoHeight);

                imagedestroy($photoImage);
                imagedestroy($photoResized);
            }
        }

        // Draw photo border
        imagerectangle($image, $photoX, $photoY, $photoX + $photoWidth, $photoY + $photoHeight, $safaricomGreen);

        // Add employee details
        $detailsX = $photoX + $photoWidth + 40;
        $detailsY = $photoY + 10;
        $lineHeight = 40;

        // Name
        $fullName = strtoupper($employee['firstName'] . ' ' . $employee['lastName']);
        $fontSize = 28;
        imagestring($image, 5, $detailsX, $detailsY, "NAME: " . $fullName, $black);

        // ID Number
        imagestring($image, 5, $detailsX, $detailsY + $lineHeight, "ID NO: " . $employee['employeeId'], $darkGray);

        // Phone
        imagestring($image, 5, $detailsX, $detailsY + $lineHeight * 2, "PHONE: " . $employee['phone'], $darkGray);

        // Role
        imagestring($image, 5, $detailsX, $detailsY + $lineHeight * 3, "ROLE: " . $employee['role'], $darkGray);

        // Region
        imagestring($image, 5, $detailsX, $detailsY + $lineHeight * 4, "REGION: " . $employee['region'], $darkGray);

        // Department
        if ($employee['department']) {
            imagestring($image, 5, $detailsX, $detailsY + $lineHeight * 5, "DEPT: " . $employee['department'], $darkGray);
        }

        // Site
        if ($employee['site']) {
            $offset = $employee['department'] ? $lineHeight * 6 : $lineHeight * 5;
            imagestring($image, 5, $detailsX, $detailsY + $offset, "SITE: " . $employee['site'], $darkGray);
        }

        // Draw red separator line
        $separatorY = $photoY + $photoHeight + 30;
        imageline($image, 50, $separatorY, $width - 50, $separatorY, $safaricomRed);
        imageline($image, 50, $separatorY + 3, $width - 50, $separatorY + 3, $safaricomRed);

        // Add footer information
        $footerY = $separatorY + 40;

        // Valid until date
        $validUntil = date('Y') + 1;
        imagestring($image, 5, 50, $footerY, "VALID UNTIL: DECEMBER 31, " . $validUntil, $darkGray);

        // Company tagline
        $tagline = "Maayash Communications - Your Trusted Safaricom Partner";
        imagestring($image, 4, 50, $footerY + 40, $tagline, $lightGray);

        // Generate and draw QR code
        $qrData = json_encode([
            'employeeId' => $employee['employeeId'],
            'name' => $employee['firstName'] . ' ' . $employee['lastName'],
            'phone' => $employee['phone'],
            'role' => $employee['role'],
            'region' => $employee['region'],
            'company' => 'Maayash Communications',
            'validUntil' => $validUntil . '-12-31'
        ]);

        // For QR code generation, you would need a library like phpqrcode
        // For now, we'll create a placeholder
        $qrX = $width - 200;
        $qrY = $separatorY + 20;
        $qrSize = 150;

        // Draw QR code placeholder (in production, use actual QR code library)
        imagefilledrectangle($image, $qrX, $qrY, $qrX + $qrSize, $qrY + $qrSize, $white);
        imagerectangle($image, $qrX, $qrY, $qrX + $qrSize, $qrY + $qrSize, $black);
        imagestring($image, 3, $qrX + 35, $qrY + 65, "QR CODE", $black);

        // Add watermark
        $watermark = 'MAAYASH COMMUNICATIONS';
        $watermarkColor = imagecolorallocatealpha($image, 0, 153, 51, 50);
        imagestring($image, 5, $width - 300, $height - 30, $watermark, $watermarkColor);

        // Convert to base64
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    /**
     * Draw centered text
     */
    private function drawCenteredText($image, $text, $fontSize, $color, $y) {
        $fontWidth = imagefontwidth(5);
        $textWidth = strlen($text) * $fontWidth;
        $x = (imagesx($image) - $textWidth) / 2;
        imagestring($image, 5, $x, $y, $text, $color);
    }

    /**
     * Load photo from base64 data
     */
    private function loadPhoto($photoData) {
        // Remove data URL prefix if present
        if (strpos($photoData, 'data:image/') === 0) {
            $photoData = substr($photoData, strpos($photoData, ',') + 1);
        }

        $imageData = base64_decode($photoData);
        if (!$imageData) {
            return null;
        }

        return imagecreatefromstring($imageData);
    }

    /**
     * Add rounded corners to image
     */
    private function addRoundedCorners(&$image, $width, $height, $radius) {
        // This is a simplified version
        // In production, use a proper rounded corners implementation
    }

    /**
     * Log download
     */
    private function logDownload($employeeId, $downloadType, $paymentId = null, $downloadCodeId = null) {
        $sql = "INSERT INTO DownloadHistory (id, employeeId, downloadType, paymentId, downloadCodeId, ipAddress, userAgent, createdAt)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $this->generateUUID(),
            $employeeId,
            $downloadType,
            $paymentId,
            $downloadCodeId,
            $this->getClientIP(),
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }

    /**
     * Save ID card to file
     */
    private function saveIDCard($imageData, $employeeId) {
        $filename = 'Maayash_ID_' . $employeeId . '_' . time() . '.png';
        $filepath = ID_CARD_DIR . $filename;

        // Ensure directory exists
        if (!is_dir(ID_CARD_DIR)) {
            mkdir(ID_CARD_DIR, 0755, true);
        }

        // Save file
        $imageData = substr($imageData, strpos($imageData, ',') + 1);
        file_put_contents($filepath, base64_decode($imageData));

        return $filename;
    }

    /**
     * Get client IP
     */
    private function getClientIP() {
        $ip = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
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
