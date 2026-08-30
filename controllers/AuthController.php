<?php
// controllers/AuthController.php
require_once __DIR__ . '/../config/db.php';

/**
 * Requires the current session user to have one of the given roles.
 * Redirects to login if not authenticated; 403 if wrong role.
 */
function requireRole(array $roles): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php?action=login');
        exit;
    }
    if (!in_array($_SESSION['role'], $roles, true)) {
        http_response_code(403);
        echo '<h2>403 — Access Denied</h2><p>You do not have permission to view this page.</p>';
        echo '<a href="index.php">Go back</a>';
        exit;
    }
}

class AuthController {

    public function showLogin(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!empty($_SESSION['user_id'])) {
            $this->redirectToDashboard($_SESSION['role']);
        }
        require __DIR__ . '/../views/login.php';
    }

    public function handleLogin(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Email and password are required.';
            header('Location: index.php?action=login');
            exit;
        }

        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT user_id, name, password_hash, role FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['login_error'] = 'Invalid email or password.';
            header('Location: index.php?action=login');
            exit;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];
        unset($_SESSION['login_error']);

        $this->redirectToDashboard($user['role']);
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_unset();
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }

    private function redirectToDashboard(string $role): void {
        $map = [
            'employee'  => 'employee_dashboard',
            'manager'   => 'manager_dashboard',
            'finance'   => 'finance_dashboard',
            'materials' => 'materials_dashboard',
            'admin'     => 'audit_log',
        ];
        $action = $map[$role] ?? 'login';
        header('Location: index.php?action=' . $action);
        exit;
    }
}
