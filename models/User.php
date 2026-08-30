<?php
// models/User.php
require_once __DIR__ . '/../config/db.php';

class User {

    public static function findByEmail(string $email): ?array {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT user_id, name, email, password_hash, role, dept_id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findById(int $userId): ?array {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT user_id, name, email, role, dept_id FROM users WHERE user_id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function getAll(): array {
        $pdo  = getDB();
        $stmt = $pdo->query('SELECT u.user_id, u.name, u.email, u.role, d.name AS dept_name FROM users u LEFT JOIN departments d ON u.dept_id = d.dept_id ORDER BY u.user_id ASC');
        return $stmt->fetchAll();
    }
}
