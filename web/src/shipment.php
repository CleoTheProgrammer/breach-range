<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/flags.php';
if (empty($_SESSION['uid'])) { header('Location: /login.php'); exit; }
// A01 Broken Access Control (IDOR): any logged-in user can read any shipment id,
// including the admin-held KF-100042 that carries a flag in its notes.
$id = (int)($_GET['id'] ?? 0);
$s = db()->prepare("SELECT * FROM shipments WHERE id = ?");  // no owner check
$s->execute([$id]);
$s = $s->fetch(PDO::FETCH_ASSOC);
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card">
<?php if ($s): ?>
  <h2 style="margin-top:0">Shipment <?= htmlspecialchars($s['tracking']) ?></h2>
  <table>
    <tr><th>Route</th><td><?= htmlspecialchars($s['origin'].' to '.$s['destination']) ?></td></tr>
    <tr><th>Status</th><td><?= htmlspecialchars($s['status']) ?></td></tr>
    <tr><th>Declared value</th><td>R <?= htmlspecialchars($s['value_zar']) ?></td></tr>
    <tr><th>Internal notes</th><td><?= $s['notes'] === '__RESTRICTED__' ? pf('idor') : htmlspecialchars($s['notes']) ?></td></tr>
  </table>
<?php else: ?><p>Shipment not found.</p><?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
