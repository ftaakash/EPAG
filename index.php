<?php
// index.php — Front Controller / Router
session_start();

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/RequestController.php';
require_once __DIR__ . '/controllers/ApprovalController.php';
require_once __DIR__ . '/controllers/VendorController.php';
require_once __DIR__ . '/controllers/AuditController.php';

$action = $_GET['action'] ?? 'login';

// Route map: action => [controller_class, method, http_method]
// http_method: 'GET', 'POST', or 'ANY'
$routes = [
    'login'               => ['AuthController',     'showLogin',             'GET'],
    'login_post'          => ['AuthController',     'handleLogin',           'POST'],
    'logout'              => ['AuthController',     'logout',                'ANY'],

    'employee_dashboard'  => ['RequestController',  'showEmployeeDashboard', 'GET'],
    'request_create'      => ['RequestController',  'create',                'POST'],

    'manager_dashboard'   => ['ApprovalController', 'showManagerDashboard',  'GET'],
    'finance_dashboard'   => ['ApprovalController', 'showFinanceDashboard',  'GET'],
    'materials_dashboard' => ['ApprovalController', 'showMaterialsDashboard','GET'],
    'approve'             => ['ApprovalController', 'approve',               'POST'],
    'reject'              => ['ApprovalController', 'reject',                'POST'],

    'vendor_create'       => ['VendorController',   'create',                'POST'],

    'audit_log'           => ['AuditController',    'showAuditLog',          'GET'],
];

if (!isset($routes[$action])) {
    header('Location: index.php?action=login');
    exit;
}

[$class, $method, $httpMethod] = $routes[$action];

// Enforce HTTP method guard
if ($httpMethod !== 'ANY' && $_SERVER['REQUEST_METHOD'] !== $httpMethod) {
    http_response_code(405);
    echo '<h2>405 Method Not Allowed</h2>';
    exit;
}

$controller = new $class();
$controller->$method();
