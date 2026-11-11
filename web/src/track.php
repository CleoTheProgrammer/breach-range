<?php
require_once __DIR__ . '/db.php';
// A03 Injection: reflected XSS on the tracking number + SQLi in the lookup.
$t = $_GET['tracking'] ?? '';
$result = null; $err = '';
if ($t !== '') {
    try {
        $sql = "SELECT tracking,origin,destination,status FROM shipments WHERE tracking = '$t'";
        $result = q_raw($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { $err = $e->getMessage(); }
}
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card">
  <h2 style="margin-top:0">Track a shipment</h2>
  <form method="get">
    <label>Tracking number</label><input name="tracking" value="<?= $t ?>">
    <button class="btn amber" type="submit">Track</button>
  </form>
  <?php if ($t !== ''): ?>
    <p>Results for <b><?= $t ?></b>:</p>  <!-- reflected unescaped -->
    <?php if ($err): ?><p style="color:#c0392b"><?= $err ?></p><?php endif; ?>
    <?php if ($result): ?>
      <table><tr><th>Tracking</th><th>Origin</th><th>Destination</th><th>Status</th></tr>
      <?php foreach ($result as $r): ?>
        <tr><td><?= $r['tracking'] ?></td><td><?= $r['origin'] ?></td>
            <td><?= $r['destination'] ?></td><td><?= $r['status'] ?></td></tr>
      <?php endforeach; ?></table>
    <?php elseif (!$err): ?><p>No shipment found.</p><?php endif; ?>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
