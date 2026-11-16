<?php
require_once __DIR__ . '/helpers.php';
// Instructor-only, same gate as the leaderboard.
if (empty($_SESSION['instructor'])) {
    $_SESSION['flash'] = ['err', 'Unlock instructor access first.'];
    header('Location: /rebonametlholofa.php'); exit;
}
$verify = audit_verify();
$rows = db()->query("SELECT * FROM audit ORDER BY id DESC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
require_once __DIR__ . '/includes/header.php'; ?>
<div class="panel">
  <div style="display:flex;align-items:center;gap:12px">
    <h2 style="margin:0">Audit log</h2>
    <a href="/rebonametlholofa.php" style="margin-left:auto;font-size:12px">leaderboard</a>
  </div>
  <p style="font-size:13px">
    <?php if ($verify['ok']): ?>
      <span class="pill done">chain intact</span> &nbsp;<?= (int)$verify['count'] ?> entries, HMAC hash chain verified end to end.
    <?php else: ?>
      <span class="pill" style="background:rgba(255,107,107,.2);color:#ff6b6b">TAMPERED</span>
      &nbsp;Chain breaks at entry #<?= htmlspecialchars((string)$verify['bad']) ?>. Everything from there on cannot be trusted.
    <?php endif; ?>
  </p>
  <table>
    <tr><th>#</th><th>Time</th><th>Operator</th><th>Action</th><th>Detail</th><th>IP</th><th>Hash</th></tr>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= (int)$r['id'] ?></td>
        <td style="color:#8aa0b4;font-size:12px"><?= htmlspecialchars($r['ts']) ?></td>
        <td><?= htmlspecialchars((string)$r['handle']) ?></td>
        <td><?= htmlspecialchars((string)$r['action']) ?></td>
        <td style="color:#8aa0b4"><?= htmlspecialchars((string)$r['detail']) ?></td>
        <td style="color:#8aa0b4;font-size:12px"><?= htmlspecialchars((string)$r['ip']) ?></td>
        <td style="color:#55697c;font-size:11px" title="<?= htmlspecialchars((string)$r['row_hash']) ?>"><?= substr((string)$r['row_hash'], 0, 12) ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="7" style="color:#8aa0b4">No audit entries yet. If this stays empty after activity, recreate the DB (docker compose down -v) so the audit table exists.</td></tr><?php endif; ?>
  </table>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
