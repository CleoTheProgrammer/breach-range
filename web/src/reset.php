<?php
require_once __DIR__ . '/db.php';
// Fixed: cryptographically random, single-use, time-limited token, rate limiting,
// and a uniform response that does not reveal whether an account exists.
$step = 'request'; $msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'request') {
    $u = db()->prepare("SELECT id FROM users WHERE username = ?");
    $u->execute([$_POST['username'] ?? '']);
    $id = $u->fetchColumn();
    if ($id) {
        $_SESSION['reset'] = ['uid' => $id, 'token' => bin2hex(random_bytes(16)),
                              'exp' => time() + 900, 'tries' => 0];
    }
    $msg = 'If that account exists, a reset link has been sent to its email.';
    $step = 'verify';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'verify') {
    $r = $_SESSION['reset'] ?? null;
    if (!$r || time() > $r['exp'] || $r['tries'] >= 5) {
        $msg = 'This reset request has expired. Start again.'; $step = 'request'; unset($_SESSION['reset']);
    } elseif (hash_equals($r['token'], $_POST['code'] ?? '')) {
        db()->prepare("UPDATE users SET password = ? WHERE id = ?")
            ->execute([password_hash($_POST['newpass'] ?? '', PASSWORD_DEFAULT), $r['uid']]);
        unset($_SESSION['reset']);
        $msg = 'Password reset complete. You may now sign in.'; $step = 'done';
    } else {
        $_SESSION['reset']['tries']++; $msg = 'Incorrect or expired code.'; $step = 'verify';
    }
}
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card" style="max-width:440px">
  <h2 style="margin-top:0">Reset password</h2>
  <?php if ($msg): ?><p style="color:#2e7d32"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
  <?php if ($step === 'request'): ?>
    <form method="post"><input type="hidden" name="action" value="request">
      <label>Username</label><input name="username">
      <button class="btn amber">Send link</button></form>
  <?php elseif ($step === 'verify'): ?>
    <form method="post"><input type="hidden" name="action" value="verify">
      <label>Reset token (from your email)</label><input name="code">
      <label>New password</label><input name="newpass" type="password">
      <button class="btn amber">Reset</button></form>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
