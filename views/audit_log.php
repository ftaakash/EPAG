<?php requireRole(['admin']); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>EPAG — Audit Log</title>
  <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<?php require __DIR__ . '/partials/navbar.php'; ?>

<main class="container">
  <h2 class="page-title">System Audit Log</h2>

  <div class="card">
    <div class="card-header">
      <h3>All Actions</h3>
      <span class="badge"><?= count($logs) ?> entries</span>
    </div>
    <div class="card-body p-0">
      <?php if (empty($logs)): ?>
        <p class="empty-state">No audit entries yet.</p>
      <?php else: ?>
        <table class="table">
          <thead>
            <tr>
              <th>Log #</th>
              <th>Request #</th>
              <th>Item</th>
              <th>Actor</th>
              <th>Action</th>
              <th>Timestamp</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($logs as $log): ?>
              <tr>
                <td><?= $log['log_id'] ?></td>
                <td><?= $log['request_id'] ?? '—' ?></td>
                <td><?= $log['item_desc'] ? htmlspecialchars($log['item_desc']) : '—' ?></td>
                <td><?= $log['actor_name'] ? htmlspecialchars($log['actor_name']) : '—' ?></td>
                <td><?= htmlspecialchars($log['action']) ?></td>
                <td><?= date('d M Y, H:i:s', strtotime($log['timestamp'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</main>

<script src="public/js/app.js"></script>
</body>
</html>
