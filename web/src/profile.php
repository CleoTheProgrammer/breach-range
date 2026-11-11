<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/flags.php';
if (empty($_SESSION['uid'])) { header('Location: /login.php'); exit; }
// A08 Mass assignment: the update binds every posted field, including 'role'.
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allowed = ['full_name','email','phone','role'];
    $sets = []; $vals = [];
    foreach ($_POST as $k => $v) {
        if (in_array($k, $allowed, true)) {
            if ($k === 'role' && $v === 'admin') { $_SESSION['escalated'] = true; }
            $sets[] = "$k = ?"; $vals[] = $v;
        }
    }
    if ($sets) {
        $vals[] = $_SESSION['uid'];
        db()->prepare("UPDATE users SET ".implode(',', $sets)." WHERE id = ?")->execute($vals);
        $u = db()->prepare("SELECT role FROM users WHERE id = ?"); $u->execute([$_SESSION['uid']]);
        $_SESSION['role'] = $u->fetchColumn();
        $msg = 'Profile saved.';
    }
}
$me = db()->prepare("SELECT username,full_name,email,phone,role,api_token FROM users WHERE id = ?");
$me->execute([$_SESSION['uid']]); $me = $me->fetch(PDO::FETCH_ASSOC);
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card" style="max-width:520px">
  <h2 style="margin-top:0">My profile</h2>
  <?php if ($msg): ?><p style="color:#2e7d32"><?= $msg ?></p><?php endif; ?>
  <?php if (($_SESSION['uname'] ?? '') === 'admin' && empty($_SESSION['escalated'])): ?>
    <p style="color:#2e7d32">Authenticated as the real administrator. Your flag: <b><?= pf('crypto') ?></b></p>
  <?php endif; ?>
  <form method="post">
    <label>Full name</label><input name="full_name" value="<?= htmlspecialchars($me['full_name']) ?>">
    <label>Email</label><input name="email" value="<?= htmlspecialchars($me['email']) ?>">
    <label>Phone</label><input name="phone" value="<?= htmlspecialchars($me['phone']) ?>">
    <button class="btn amber" type="submit">Save</button>
  </form>
  <p style="font-size:12px;color:#8296a8">Account type: <b><?= htmlspecialchars($me['role']) ?></b>
     &middot; API token: <code><?= htmlspecialchars($me['api_token']) ?></code></p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
