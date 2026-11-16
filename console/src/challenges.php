<?php
require_once __DIR__ . '/helpers.php';
$P = require_player();
$C = challenges();
$prog = progress_for($P['id']);
$launch = TARGET_URL . '/start.php?pk=' . $P['player_key'];
$flags_done = count(array_filter($prog, fn($r) => $r['flag']));
$total_ch = count($C);
$cleared = stage1_complete($P['id']);
require_once __DIR__ . '/includes/header.php'; ?>

<div class="panel">
  <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
    <div>
      <div style="color:#8aa0b4;font-size:12px;letter-spacing:2px;text-transform:uppercase">Your target</div>
      <h2 style="margin:4px 0">Kestrel Freight portal + API</h2>
    </div>
    <div style="margin-left:auto">
      <a class="btn" href="<?= htmlspecialchars($launch) ?>" target="_blank" rel="noopener">Launch target &rarr;</a>
    </div>
  </div>
  <p style="font-size:13px;color:#8aa0b4;margin-top:14px">
    Launch sets your player key on the target so your flags are personalised. For API calls, pass your key
    as <code>?pk=<?= htmlspecialchars($P['player_key']) ?></code> or header <code>X-Player-Key</code>.
    Points: <b style="color:#f4a400"><?= points_for($P['id']) ?></b> &middot; a hint costs <?= HINT_COST ?>.
  </p>
</div>

<div class="panel" style="<?= $cleared ? 'border-color:#3ad29f' : '' ?>">
  <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
    <div>
      <div style="color:#8aa0b4;font-size:12px;letter-spacing:2px;text-transform:uppercase">Stage progress</div>
      <h3 style="margin:4px 0"><?= $flags_done ?> / <?= $total_ch ?> flags captured</h3>
    </div>
    <div style="margin-left:auto">
      <?php if ($cleared): ?>
        <a class="btn" href="/stage2.php">Stage 2 unlocked &rarr;</a>
      <?php else: ?>
        <span class="pill todo">Stage 2 locked, capture all flags</span>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="panel">
  <h3 style="margin-top:0">Submit a flag</h3>
  <form method="post" action="/submit.php" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
    <input name="flag" placeholder="FLAG{...}" style="flex:1;min-width:260px;margin:0" required>
    <button class="btn" type="submit">Submit</button>
  </form>
</div>

<?php $vis = challenges_visible(); ?>
<div class="panel">
  <h3 style="margin-top:0">Challenges</h3>
  <?php if (!$vis): ?>
    <p style="color:#8aa0b4">The instructor has not released the challenges yet. They will appear here automatically, no need to refresh.</p>
  <?php else: ?>
  <table>
    <tr><th>Challenge</th><th>Classification</th><th>Surface</th><th>Status</th><th>Pts</th><th></th></tr>
    <?php foreach ($C as $id => $c):
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
  <?php endif; ?>
</div>
<script>
(function(){
  var cur = <?= $vis ? 'true' : 'false' ?>;
  setInterval(function(){
    fetch('/state.php', {cache:'no-store'})
      .then(function(r){ return r.json(); })
      .then(function(d){ if (!!d.visible !== cur) location.reload(); })
      .catch(function(){});
  }, 3000);
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
