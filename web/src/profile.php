<?php
require_once __DIR__ . '/db.php';
if (empty($_SESSION['uid'])) { header('Location: /login.php'); exit; }
// Fixed: strict allow-list of editable fields. role is NOT user-editable, so no
// mass-assignment privilege escalation. Role changes go through an admin workflow.
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allowed = ['full_name','email','phone'];            // role deliberately excluded
    $sets = []; $vals = [];
    foreach ($allowed as $k) {
        if (array_key_exists($k, $_POST)) { $sets[] = "$k = ?"; $vals[] = $_POST[$k]; }
    }
    if ($sets) {
        $vals[] = $_SESSION['uid'];
        db()->prepare("UPDATE users SET ".implode(',', $sets)." WHERE id = ?")->execute($vals);
        $msg = 'Profile saved.';
    }
}
$me = db()->prepare("SELECT username,full_name,email,phone,role FROM users WHERE id = ?");
$me->execute([$_SESSION['uid']]); $me = $me->fetch(PDO::FETCH_ASSOC);
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card" style="max-width:520px">
  <h2 style="margin-top:0">My profile</h2>
  <?php if ($msg): ?><p style="color:#2e7d32"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
  <form method="post">
    <label>Full name</label><input name="full_name" value="<?= htmlspecialchars($me['full_name']) ?>">
    <label>Email</label><input name="email" value="<?= htmlspecialchars($me['email']) ?>">
    <label>Phone</label><input name="phone" value="<?= htmlspecialchars($me['phone']) ?>">
    <button class="btn amber" type="submit">Save</button>
  </form>
  <p style="font-size:12px;color:#8296a8">Account type: <b><?= htmlspecialchars($me['role']) ?></b></p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
