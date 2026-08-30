<?php
// controllers/RequestController.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Request.php';
require_once __DIR__ . '/../models/AuditLog.php';

class RequestController {

    public function showEmployeeDashboard(): void {
        requireRole(['employee']);
        $requests = Request::findByUser($_SESSION['user_id']);
        require __DIR__ . '/../views/employee_dashboard.php';
    }

    public function create(): void {
        requireRole(['employee']);

        $itemDesc = trim($_POST['item_desc'] ?? '');
        $amount   = (float)($_POST['amount'] ?? 0);

        if (empty($itemDesc) || $amount <= 0) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Item description and a valid amount are required.'];
            header('Location: index.php?action=employee_dashboard');
            exit;
        }

        $requestId = Request::create($_SESSION['user_id'], $itemDesc, $amount);
        AuditLog::insert($requestId, $_SESSION['user_id'], 'Request created: ' . $itemDesc . ' (₹' . number_format($amount, 2) . ')');

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Request submitted successfully.'];
        header('Location: index.php?action=employee_dashboard');
        exit;
    }
}
