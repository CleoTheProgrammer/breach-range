<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/flags.php';
if (empty($_SESSION['uid'])) { header('Location: /login.php'); exit; }
$rows = db()->prepare("SELECT id,tracking,origin,destination,status,value_zar FROM shipments WHERE owner_id = ?");
$rows->execute([$_SESSION['uid']]);
$rows = $rows->fetchAll(PDO::FETCH_ASSOC);
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card">
  <h2 style="margin-top:0">My shipments</h2>
  <?php if (!empty($_SESSION['sqli_flag'])): ?>
    <p style="color:#2e7d32">Bypass detected. Your flag: <b><?= htmlspecialchars($_SESSION['sqli_flag']) ?></b></p>
  <?php endif; ?>
  <?php if (($_SESSION['uname'] ?? '') === 'driver'): ?>
    <p style="color:#2e7d32">Weak credentials, no lockout. You broke into the driver account. Your flag: <b><?= pf('authn') ?></b></p>
  <?php endif; ?>
  <table><tr><th>#</th><th>Tracking</th><th>Route</th><th>Status</th><th></th></tr>
  <?php foreach ($rows as $r): ?>
    <tr><td><?= $r['id'] ?></td><td><?= htmlspecialchars($r['tracking']) ?></td>
        <td><?= htmlspecialchars($r['origin']).' to '.htmlspecialchars($r['destination']) ?></td>
        <td><?= htmlspecialchars($r['status']) ?></td>
        <td><a href="/shipment.php?id=<?= $r['id'] ?>">view</a></td></tr>
  <?php endforeach; ?></table>
  <p style="font-size:13px"><a href="/report.php">Build a shipment report</a></p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
