<?php
require_once __DIR__ . '/db.php';
if (empty($_SESSION['uid'])) { header('Location: /login.php'); exit; }
// Fixed: ownership check. A customer may only read their own shipments; admins read all.
$id = (int)($_GET['id'] ?? 0);
if (($_SESSION['role'] ?? '') === 'admin') {
    $s = db()->prepare("SELECT * FROM shipments WHERE id = ?");
    $s->execute([$id]);
} else {
    $s = db()->prepare("SELECT * FROM shipments WHERE id = ? AND owner_id = ?");
    $s->execute([$id, $_SESSION['uid']]);
}
$s = $s->fetch(PDO::FETCH_ASSOC);
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card">
<?php if ($s): ?>
  <h2 style="margin-top:0">Shipment <?= htmlspecialchars($s['tracking']) ?></h2>
  <table>
    <tr><th>Route</th><td><?= htmlspecialchars($s['origin'].' to '.$s['destination']) ?></td></tr>
    <tr><th>Status</th><td><?= htmlspecialchars($s['status']) ?></td></tr>
    <tr><th>Declared value</th><td>R <?= htmlspecialchars($s['value_zar']) ?></td></tr>
    <tr><th>Internal notes</th><td><?= htmlspecialchars($s['notes']) ?></td></tr>
  </table>
<?php else: ?><p>Shipment not found.</p><?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
