<?php
require_once __DIR__ . '/helpers.php';
$P = require_player();
if (!stage1_complete($P['id'])) {
    $_SESSION['flash'] = ['err', 'Stage 2 is locked. Capture every Stage 1 flag first.'];
    header('Location: /challenges.php'); exit;
}
$b = stage2_brief();
$C2 = challenges_stage(2);
$prog = progress_for($P['id']);
$s2done = stage2_complete($P['id']);
$s2flags = count(array_filter(array_intersect_key($prog, $C2), fn($r) => $r['flag']));
require_once __DIR__ . '/includes/header.php'; ?>
<div class="panel">
  <span class="pill done">Stage 1 cleared</span>
  <?php if ($s2done): ?><span class="pill done" style="margin-left:6px">Stage 2 cleared</span><?php endif; ?>
  <div style="color:#8aa0b4;font-size:12px;letter-spacing:2px;text-transform:uppercase;margin-top:12px">Operation brief, Stage 2</div>
  <h1 style="margin:6px 0 16px"><?= htmlspecialchars($b['title']) ?></h1>
  <p style="line-height:1.6"><?= htmlspecialchars($b['hook']) ?></p>
  <h3 style="color:#f4a400;margin-bottom:6px">Objectives</h3>
  <ul style="line-height:1.7">
    <?php foreach ($b['objectives'] as $o): ?><li><?= htmlspecialchars($o) ?></li><?php endforeach; ?>
  </ul>
</div>

<div class="panel">
  <h3 style="margin-top:0">Reaching the internal network</h3>
  <p style="line-height:1.6;color:#8aa0b4">These services are not exposed to you directly, they sit inside Kestrel's
     corporate network. Reach them from your foothold on the perimeter host (the webshell or reverse shell you planted in
     Stage 1), resolving them by name. Pass your player key so flags are personalised to you.</p>
  <pre style="background:#0c131b;color:#d6e6f2;padding:14px;border-radius:6px;overflow:auto"># from your shell on the portal host (www-data):
curl -H "X-Player-Key: <?= htmlspecialchars($P['player_key']) ?>" http://intranet/
curl -H "X-Player-Key: <?= htmlspecialchars($P['player_key']) ?>" "http://finance/?pw=&lt;looted-password&gt;"
curl -H "X-Player-Key: <?= htmlspecialchars($P['player_key']) ?>" "http://secrets/?debug=1"</pre>
</div>

<div class="panel">
  <h3 style="margin-top:0">Submit a flag</h3>
  <form method="post" action="/submit.php" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
    <input name="flag" placeholder="FLAG{...}" style="flex:1;min-width:260px;margin:0" required>
    <button class="btn" type="submit">Submit</button>
  </form>
</div>

<div class="panel">
  <h3 style="margin-top:0">Internal targets (<?= $s2flags ?> / <?= count($C2) ?>)</h3>
  <table>
    <tr><th>Target</th><th>Classification</th><th>Surface</th><th>Status</th><th>Pts</th><th></th></tr>
    <?php foreach ($C2 as $id => $c):
      $pr = $prog[$id] ?? ['flag'=>false,'quiz'=>false,'hint'=>false];
      $pts = ($pr['flag']?PTS_FLAG:0) + ($pr['quiz']?PTS_QUIZ:0) - ($pr['hint']?HINT_COST:0);
      if ($pr['quiz']) { $pill='done'; $label='Complete'; }
      elseif ($pr['flag']) { $pill='flag'; $label='Flag captured'; }
      else { $pill='todo'; $label='Not started'; }
    ?>
    <tr>
      <td><b><?= htmlspecialchars($c['title']) ?></b></td>
      <td style="color:#8aa0b4"><?= htmlspecialchars($c['owasp']) ?></td>
      <td><?= strtoupper($c['surface']) ?></td>
      <td><span class="pill <?= $pill ?>"><?= $label ?></span></td>
      <td><?= $pts ?></td>
      <td>
        <?php if ($pr['flag']): ?>
          <a href="/lesson.php?v=<?= $id ?>"><?= $pr['quiz']?'review':'learn + quiz' ?> &rarr;</a>
        <?php elseif (!$pr['hint']): ?>
          <form method="post" action="/hint.php" style="display:inline;margin:0">
            <input type="hidden" name="v" value="<?= $id ?>">
            <button class="btn ghost" style="padding:4px 12px;font-size:12px" type="submit">hint (-<?= HINT_COST ?>)</button>
          </form>
        <?php else: ?><span style="color:#8aa0b4;font-size:12px">hint used</span><?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <p style="font-size:12px;color:#8aa0b4"><a href="/challenges.php">&larr; Back to Stage 1</a></p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
