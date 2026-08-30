<?php
// controllers/ApprovalController.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Request.php';
require_once __DIR__ . '/../models/Approval.php';
require_once __DIR__ . '/../models/AuditLog.php';
require_once __DIR__ . '/../models/Vendor.php';

class ApprovalController {

    // Stage definitions: role => [required_status, next_status, stage_key]
    private const STAGES = [
        'manager'   => ['pending_manager',   'pending_finance',    'manager'],
        'finance'   => ['pending_finance',   'pending_materials',  'finance'],
        'materials' => ['pending_materials', 'fulfilled',          'materials'],
    ];

    public function showManagerDashboard(): void {
        requireRole(['manager']);
        $requests = Request::findByStatus('pending_manager');
        require __DIR__ . '/../views/manager_dashboard.php';
    }

    public function showFinanceDashboard(): void {
        requireRole(['finance']);
        $requests = Request::findByStatus('pending_finance');
        require __DIR__ . '/../views/finance_dashboard.php';
    }

    public function showMaterialsDashboard(): void {
        requireRole(['materials']);
        $requests = Request::findByStatus('pending_materials');
        $vendors  = Vendor::getApproved();
        require __DIR__ . '/../views/materials_dashboard.php';
    }

    public function approve(): void {
        $role      = $_SESSION['role'];
        $approverId = $_SESSION['user_id'];
        $requestId = (int)($_POST['request_id'] ?? 0);
        $vendorId  = !empty($_POST['vendor_id']) ? (int)$_POST['vendor_id'] : null;

        if (!isset(self::STAGES[$role])) {
            http_response_code(403);
            exit('Forbidden');
        }

        [$requiredStatus, $nextStatus, $stageKey] = self::STAGES[$role];

        $request = Request::findById($requestId);
        if (!$request || $request['status'] !== $requiredStatus) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Request not found or invalid status.'];
            $this->redirectToRoleDashboard($role);
        }

        // Atomic: record approval + advance status + audit log
        $pdo = getDB();
        $pdo->beginTransaction();
        try {
            Approval::record($requestId, $approverId, $stageKey, 'approved');
            Request::updateStatus($requestId, $nextStatus, $role === 'materials' ? $vendorId : null);
            AuditLog::insert($requestId, $approverId, ucfirst($stageKey) . ' approved request #' . $requestId . ($role === 'materials' && $vendorId ? ' (vendor assigned)' : ''));
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Approval failed: ' . $e->getMessage()];
            $this->redirectToRoleDashboard($role);
        }

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Request #' . $requestId . ' approved.'];
        $this->redirectToRoleDashboard($role);
    }

    public function reject(): void {
        $role       = $_SESSION['role'];
        $approverId = $_SESSION['user_id'];
        $requestId  = (int)($_POST['request_id'] ?? 0);

        if (!isset(self::STAGES[$role])) {
            http_response_code(403);
            exit('Forbidden');
        }

        [$requiredStatus, , $stageKey] = self::STAGES[$role];

        $request = Request::findById($requestId);
        if (!$request || $request['status'] !== $requiredStatus) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Request not found or invalid status.'];
            $this->redirectToRoleDashboard($role);
        }

        $pdo = getDB();
        $pdo->beginTransaction();
        try {
            Approval::record($requestId, $approverId, $stageKey, 'rejected');
            Request::updateStatus($requestId, 'rejected');
            AuditLog::insert($requestId, $approverId, ucfirst($stageKey) . ' rejected request #' . $requestId);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Rejection failed: ' . $e->getMessage()];
            $this->redirectToRoleDashboard($role);
        }

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Request #' . $requestId . ' rejected.'];
        $this->redirectToRoleDashboard($role);
    }

    private function redirectToRoleDashboard(string $role): void {
        $map = [
            'manager'   => 'manager_dashboard',
            'finance'   => 'finance_dashboard',
            'materials' => 'materials_dashboard',
        ];
        header('Location: index.php?action=' . ($map[$role] ?? 'login'));
        exit;
    }
}
