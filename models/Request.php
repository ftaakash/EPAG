<?php
// models/Request.php
require_once __DIR__ . '/../config/db.php';

class Request {

    public static function create(int $userId, string $itemDesc, float $amount): int {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'INSERT INTO requests (user_id, item_desc, amount, status) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $itemDesc, $amount, 'pending_manager']);
        return (int)$pdo->lastInsertId();
    }

    public static function findById(int $requestId): ?array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT r.*, u.name AS requester_name, v.name AS vendor_name
             FROM requests r
             JOIN users u ON r.user_id = u.user_id
             LEFT JOIN vendors v ON r.vendor_id = v.vendor_id
             WHERE r.request_id = ?'
        );
        $stmt->execute([$requestId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByUser(int $userId): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT r.*, v.name AS vendor_name
             FROM requests r
             LEFT JOIN vendors v ON r.vendor_id = v.vendor_id
             WHERE r.user_id = ?
             ORDER BY r.created_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function findByStatus(string $status): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT r.*, u.name AS requester_name
             FROM requests r
             JOIN users u ON r.user_id = u.user_id
             WHERE r.status = ?
             ORDER BY r.created_at ASC'
        );
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }

    public static function updateStatus(int $requestId, string $status, ?int $vendorId = null): void {
        $pdo = getDB();
        if ($vendorId !== null) {
            $stmt = $pdo->prepare('UPDATE requests SET status = ?, vendor_id = ? WHERE request_id = ?');
            $stmt->execute([$status, $vendorId, $requestId]);
        } else {
            $stmt = $pdo->prepare('UPDATE requests SET status = ? WHERE request_id = ?');
            $stmt->execute([$status, $requestId]);
        }
    }
}
