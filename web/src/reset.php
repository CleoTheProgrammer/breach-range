<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/flags.php';
// A04 Insecure design + A07: reset code is a predictable 4-digit number, no rate limit,
// no lockout, and the code is derived from the user id so it is trivially guessable.
$step = $_GET['step'] ?? 'request'; $msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'request') {
    $u = db()->prepare("SELECT id FROM users WHERE username = ?"); $u->execute([$_POST['username'] ?? '']);
    $id = $u->fetchColumn();
    if ($id) { $_SESSION['reset_user'] = $id; $msg = 'A 4-digit code was sent to the account email.'; }
    else { $msg = 'No such user.'; } // enumeration again
    $step = 'verify';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'verify') {
    $id = $_SESSION['reset_user'] ?? 0;
    $expected = str_pad((string)((($id * 1337) + 42) % 10000), 4, '0', STR_PAD_LEFT); // derived from id
    if (($_POST['code'] ?? '') === $expected) {
        db()->prepare("UPDATE users SET password = MD5(?) WHERE id = ?")->execute([$_POST['newpass'] ?? '', $id]);
        $msg = 'Password reset complete. Your flag: ' . pf('idesign'); $step = 'done';
    } else { $msg = 'Incorrect code.'; $step = 'verify'; }
}
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card" style="max-width:440px">
  <h2 style="margin-top:0">Reset password</h2>
  <?php if ($msg): ?><p style="color:#2e7d32"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
  <?php if ($step === 'request'): ?>
    <form method="post"><input type="hidden" name="action" value="request">
      <label>Username</label><input name="username">
      <button class="btn amber">Send code</button></form>
  <?php elseif ($step === 'verify'): ?>
    <form method="post"><input type="hidden" name="action" value="verify">
      <label>4-digit code</label><input name="code" maxlength="4">
      <label>New password</label><input name="newpass" type="password">
      <button class="btn amber">Reset</button></form>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
