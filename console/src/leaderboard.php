<?php
require_once __DIR__ . '/helpers.php';
$rows = leaderboard();
$total = count(challenges()) * (PTS_FLAG + PTS_QUIZ);
require_once __DIR__ . '/includes/header.php'; ?>
<div class="panel">
  <h2 style="margin-top:0">Leaderboard</h2>
  <p style="color:#8aa0b4;font-size:13px">Ranked by points, ties broken by who got there first. Max possible: <b><?= $total ?></b> (hints cost points).</p>
  <table>
    <tr><th>#</th><th>Operator</th><th>Points</th><th>Progress</th><th>Stage 1</th></tr>
    <?php foreach ($rows as $i => $r): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><b><?= htmlspecialchars($r['handle']) ?></b></td>
        <td style="color:#f4a400"><?= $r['points'] ?></td>
        <td style="color:#8aa0b4"><?= $total ? round(100*$r['points']/$total) : 0 ?>%</td>
        <td><?php if ($r['cleared']): ?><span class="pill done">cleared</span><?php else: ?><span class="pill todo">in progress</span><?php endif; ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="5" style="color:#8aa0b4">No operators enlisted yet.</td></tr><?php endif; ?>
  </table>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
