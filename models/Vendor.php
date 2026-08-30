<?php
// models/Vendor.php
require_once __DIR__ . '/../config/db.php';

class Vendor {

    public static function getApproved(): array {
        $pdo  = getDB();
        $stmt = $pdo->query('SELECT * FROM vendors WHERE approved = 1 ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    public static function getAll(): array {
        $pdo  = getDB();
        $stmt = $pdo->query('SELECT * FROM vendors ORDER BY name ASC');
        return $stmt->fetchAll();
    }
}
