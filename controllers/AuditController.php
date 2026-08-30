<?php
// controllers/AuditController.php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/AuditLog.php';

class AuditController {

    public function showAuditLog(): void {
        requireRole(['admin']);
        $logs = AuditLog::getAll();
        require __DIR__ . '/../views/audit_log.php';
    }
}
