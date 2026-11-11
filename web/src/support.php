<?php
require_once __DIR__ . '/db.php';
if (empty($_SESSION['uid'])) { header('Location: /login.php'); exit; }
// Fixed: parameterised insert and encoded output (no stored XSS). A strict CSP
// is also set in the Apache config as defence in depth.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    db()->prepare("INSERT INTO support_tickets (owner_id,subject,body) VALUES (?,?,?)")
        ->execute([$_SESSION['uid'], $_POST['subject'] ?? '', $_POST['body'] ?? '']);
}
$tix = db()->query("SELECT subject,body,created_at FROM support_tickets ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card">
  <h2 style="margin-top:0">Open a support ticket</h2>
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
      <div><?= nl2br(htmlspecialchars($t['body'])) ?></div>
      <small style="color:#8296a8"><?= htmlspecialchars($t['created_at']) ?></small>
    </div>
  <?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
