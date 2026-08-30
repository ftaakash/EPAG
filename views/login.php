<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EPAG — Login</title>
  <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="auth-page">
  <div class="auth-card">
    <div class="auth-brand">
      <div class="auth-logo">⚙</div>
      <h1>EPAG</h1>
      <p class="auth-tagline">Enterprise Procurement Approval Gateway</p>
    </div>

    <?php if (!empty($_SESSION['login_error'])): ?>
      <div class="alert alert-error">
        <?= htmlspecialchars($_SESSION['login_error']) ?>
        <?php unset($_SESSION['login_error']); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=login_post" id="loginForm">
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="you@epag.com" required autocomplete="email">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-primary btn-block" id="loginBtn">Sign In</button>
    </form>

    <p class="auth-hint">Demo: use any role email with password <strong>password</strong></p>
  </div>

  <script src="public/js/app.js"></script>
</body>
</html>
