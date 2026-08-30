<?php
// models/AuditLog.php
require_once __DIR__ . '/../config/db.php';

class AuditLog {

    public static function insert(?int $requestId, ?int $actorId, string $action): void {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'INSERT INTO audit_log (request_id, actor_id, action) VALUES (?, ?, ?)'
        );
        $stmt->execute([$requestId, $actorId, $action]);
    }

    public static function getAll(): array {
        $pdo  = getDB();
        $stmt = $pdo->query(
            'SELECT al.*, u.name AS actor_name, r.item_desc
             FROM audit_log al
             LEFT JOIN users u ON al.actor_id = u.user_id
             LEFT JOIN requests r ON al.request_id = r.request_id
             ORDER BY al.timestamp DESC'
        );
        return $stmt->fetchAll();
    }
}
