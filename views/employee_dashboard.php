<?php
// views/employee_dashboard.php
requireRole(['employee']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>EPAG — Employee Dashboard</title>
  <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<?php require __DIR__ . '/partials/navbar.php'; ?>

<main class="container">
  <h2 class="page-title">My Requests</h2>

  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
      <?= htmlspecialchars($_SESSION['flash']['msg']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <!-- New Request Form -->
  <div class="card">
    <div class="card-header">
      <h3>New Procurement Request</h3>
    </div>
    <div class="card-body">
      <form method="POST" action="index.php?action=request_create" id="requestForm">
        <div class="form-row">
          <div class="form-group flex-2">
            <label for="item_desc">Item Description</label>
            <input type="text" id="item_desc" name="item_desc" placeholder="e.g. 10x USB-C cables" required maxlength="255">
          </div>
          <div class="form-group flex-1">
            <label for="amount">Amount (₹)</label>
            <input type="number" id="amount" name="amount" placeholder="0.00" step="0.01" min="1" required>
          </div>
          <div class="form-group form-submit-col">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-primary" id="submitBtn">Submit Request</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Request Table -->
  <div class="card mt-4">
    <div class="card-header">
      <h3>Request History</h3>
      <span class="badge"><?= count($requests) ?> total</span>
    </div>
    <div class="card-body p-0">
      <?php if (empty($requests)): ?>
        <p class="empty-state">No requests yet. Submit one above!</p>
      <?php else: ?>
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Item Description</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Vendor</th>
              <th>Submitted</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($requests as $req): ?>
              <tr>
                <td><?= $req['request_id'] ?></td>
                <td><?= htmlspecialchars($req['item_desc']) ?></td>
                <td>₹<?= number_format($req['amount'], 2) ?></td>
                <td><span class="status-badge status-<?= str_replace('_', '-', $req['status']) ?>"><?= ucwords(str_replace('_', ' ', $req['status'])) ?></span></td>
                <td><?= $req['vendor_name'] ? htmlspecialchars($req['vendor_name']) : '—' ?></td>
                <td><?= date('d M Y, H:i', strtotime($req['created_at'])) ?></td>
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
