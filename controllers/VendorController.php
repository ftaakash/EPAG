<?php
// controllers/VendorController.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Vendor.php';
require_once __DIR__ . '/../models/AuditLog.php';

class VendorController {

    public function create(): void {
        requireRole(['materials']);

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Vendor name cannot be empty.'];
        } else {
            $vendorId = Vendor::create($name, true);
            $userId   = $_SESSION['user_id'] ?? null;
            if ($userId) {
                AuditLog::insert(null, $userId, 'Added new vendor "' . $name . '" (ID #' . $vendorId . ')');
            }
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Vendor "' . htmlspecialchars($name) . '" added successfully.'];
        }

        header('Location: index.php?action=materials_dashboard');
        exit;
    }
}
