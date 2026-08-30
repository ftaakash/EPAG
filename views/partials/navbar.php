<?php
// views/partials/navbar.php
// Assumes session already started by controller
$role     = $_SESSION['role'] ?? '';
$name     = $_SESSION['name'] ?? 'User';
$navLinks = [
    'employee'  => [['employee_dashboard', '📋 My Requests']],
    'manager'   => [['manager_dashboard',  '✅ Approvals']],
    'finance'   => [['finance_dashboard',  '💰 Finance Review']],
    'materials' => [['materials_dashboard','📦 Materials Queue']],
    'admin'     => [['audit_log',          '🔍 Audit Log']],
];
$links = $navLinks[$role] ?? [];
?>
<nav class="navbar">
  <div class="nav-brand">
    <span class="nav-logo">⚙</span>
    <span class="nav-title">EPAG</span>
  </div>
  <div class="nav-links">
    <?php foreach ($links as [$action, $label]): ?>
      <a href="index.php?action=<?= $action ?>" class="nav-link <?= (($_GET['action'] ?? '') === $action) ? 'active' : '' ?>">
        <?= $label ?>
      </a>
    <?php endforeach; ?>
  </div>
  <div class="nav-user">
    <span class="nav-name"><?= htmlspecialchars($name) ?></span>
    <span class="role-badge role-<?= $role ?>"><?= ucfirst($role) ?></span>
    <a href="index.php?action=logout" class="btn btn-outline btn-sm">Logout</a>
  </div>
</nav>
