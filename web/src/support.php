<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/flags.php';
if (empty($_SESSION['uid'])) { header('Location: /login.php'); exit; }
// A03 Injection: stored XSS. Body is saved raw and rendered raw. If a player lands
// a working script payload, award the per-player xss flag.
$earned = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = $_POST['body'] ?? '';
    $ins = db()->prepare("INSERT INTO support_tickets (owner_id,subject,body) VALUES (?,?,?)");
    $ins->execute([$_SESSION['uid'], $_POST['subject'] ?? '', $body]);
    if (preg_match('/<script|onerror\s*=|onload\s*=|onmouseover\s*=/i', $body)) {
        $earned = pf('xss');
    }
}
$tix = q_raw("SELECT subject,body,created_at FROM support_tickets ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card">
  <h2 style="margin-top:0">Open a support ticket</h2>
  <?php if ($earned): ?><p style="color:#2e7d32">Script payload stored. Your flag: <b><?= htmlspecialchars($earned) ?></b></p><?php endif; ?>
  <form method="post">
    <label>Subject</label><input name="subject">
    <label>Message</label><textarea name="body" rows="4"></textarea>
    <button class="btn amber" type="submit">Submit ticket</button>
  </form>
</div>
<div class="card">
  <h3 style="margin-top:0">Recent tickets (all customers)</h3>
  <?php foreach ($tix as $t): ?>
    <div style="border-bottom:1px solid #eee;padding:8px 0">
      <b><?= htmlspecialchars($t['subject']) ?></b>
      <div><?= $t['body'] ?></div>
      <small style="color:#8296a8"><?= $t['created_at'] ?></small>
    </div>
  <?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
