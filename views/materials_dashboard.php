<?php requireRole(['materials']); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>EPAG — Materials Dashboard</title>
  <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<?php require __DIR__ . '/partials/navbar.php'; ?>

<main class="container">
  <h2 class="page-title">Materials Procurement Queue</h2>

  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
      <?= htmlspecialchars($_SESSION['flash']['msg']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
      <h3>➕ Add New Vendor</h3>
    </div>
    <div class="card-body">
      <form method="POST" action="index.php?action=vendor_create" style="display:flex;gap:12px;align-items:center;max-width:600px;">
        <input type="text" name="name" placeholder="Vendor Name (e.g. Acme Industrial Supplies)" required style="flex:1;padding:8px 14px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-size:0.9rem;">
        <button type="submit" class="btn btn-primary">Add Vendor</button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h3>Requests Ready for Fulfillment</h3>
      <span class="badge"><?= count($requests) ?> pending</span>
    </div>
    <div class="card-body p-0">
      <?php if (empty($requests)): ?>
        <p class="empty-state">✓ No requests pending materials fulfillment.</p>
      <?php else: ?>
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Employee</th>
              <th>Item Description</th>
              <th>Amount</th>
              <th>Submitted</th>
              <th>Assign Vendor &amp; Action</th>
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
                  <form method="POST" action="index.php?action=approve" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                    <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                    <select name="vendor_id" class="form-select-sm" required>
                      <option value="">Select vendor…</option>
                      <?php foreach ($vendors as $v): ?>
                        <option value="<?= $v['vendor_id'] ?>"><?= htmlspecialchars($v['name']) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-success btn-sm">Fulfill</button>
                  </form>
                  <form method="POST" action="index.php?action=reject" style="display:inline;margin-top:4px">
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
