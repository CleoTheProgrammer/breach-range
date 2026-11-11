<?php
require_once __DIR__ . '/db.php';
// Fixed: parameterised lookup (no SQLi) and encoded output (no reflected XSS).
$t = $_GET['tracking'] ?? '';
$result = [];
if ($t !== '') {
    $s = db()->prepare("SELECT tracking,origin,destination,status FROM shipments WHERE tracking = ?");
    $s->execute([$t]);
    $result = $s->fetchAll(PDO::FETCH_ASSOC);
}
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card">
  <h2 style="margin-top:0">Track a shipment</h2>
  <form method="get">
    <label>Tracking number</label><input name="tracking" value="<?= htmlspecialchars($t) ?>">
    <button class="btn amber" type="submit">Track</button>
  </form>
  <?php if ($t !== ''): ?>
    <p>Results for <b><?= htmlspecialchars($t) ?></b>:</p>
    <?php if ($result): ?>
      <table><tr><th>Tracking</th><th>Origin</th><th>Destination</th><th>Status</th></tr>
      <?php foreach ($result as $r): ?>
        <tr><td><?= htmlspecialchars($r['tracking']) ?></td><td><?= htmlspecialchars($r['origin']) ?></td>
            <td><?= htmlspecialchars($r['destination']) ?></td><td><?= htmlspecialchars($r['status']) ?></td></tr>
      <?php endforeach; ?></table>
    <?php else: ?><p>No shipment found.</p><?php endif; ?>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
