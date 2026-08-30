<?php
// models/Approval.php
require_once __DIR__ . '/../config/db.php';

class Approval {

    public static function record(int $requestId, int $approverId, string $stage, string $decision): void {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'INSERT INTO approvals (request_id, approver_id, stage, decision) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$requestId, $approverId, $stage, $decision]);
    }

    public static function findByRequest(int $requestId): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT a.*, u.name AS approver_name
             FROM approvals a
             JOIN users u ON a.approver_id = u.user_id
             WHERE a.request_id = ?
             ORDER BY a.timestamp ASC'
        );
        $stmt->execute([$requestId]);
        return $stmt->fetchAll();
    }
}
