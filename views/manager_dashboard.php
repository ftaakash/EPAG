<?php requireRole(['manager']); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>EPAG — Manager Dashboard</title>
  <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<?php require __DIR__ . '/partials/navbar.php'; ?>

<main class="container">
  <h2 class="page-title">Pending Manager Approvals</h2>

  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
      <?= htmlspecialchars($_SESSION['flash']['msg']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <div class="card">
    <div class="card-header">
      <h3>Requests Awaiting Manager Review</h3>
      <span class="badge"><?= count($requests) ?> pending</span>
    </div>
    <div class="card-body p-0">
      <?php if (empty($requests)): ?>
        <p class="empty-state">✓ No requests pending manager review.</p>
      <?php else: ?>
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Employee</th>
              <th>Item Description</th>
              <th>Amount</th>
              <th>Submitted</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($requests as $req): ?>
              <tr>
                <td><?= $req['request_id'] ?></td>
                <td><?= htmlspecialchars($req['requester_name']) ?></td>
                <td><?= htmlspecialchars($req['item_desc']) ?></td>
                <td>₹<?= number_format($req['amount'], 2) ?></td>
                <td><?= date('d M Y', strtotime($req['created_at'])) ?></td>
                <td class="action-cell">
                  <form method="POST" action="index.php?action=approve" style="display:inline">
                    <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                  </form>
                  <form method="POST" action="index.php?action=reject" style="display:inline">
                    <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Reject request #<?= $req['request_id'] ?>?')">Reject</button>
                  </form>
                </td>
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
