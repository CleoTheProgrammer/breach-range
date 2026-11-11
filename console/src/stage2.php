<?php
require_once __DIR__ . '/helpers.php';
$P = require_player();
if (!stage1_complete($P['id'])) {
    $_SESSION['flash'] = ['err', 'Stage 2 is locked. Capture every Stage 1 flag first.'];
    header('Location: /challenges.php'); exit;
}
$b = stage2_brief();
$full = stage1_fullmarks($P['id']);
require_once __DIR__ . '/includes/header.php'; ?>
<div class="panel">
  <span class="pill done">Stage 1 cleared</span>
  <?php if ($full): ?><span class="pill done" style="margin-left:6px">Full marks</span><?php endif; ?>
  <div style="color:#8aa0b4;font-size:12px;letter-spacing:2px;text-transform:uppercase;margin-top:12px">Operation brief, Stage 2</div>
  <h1 style="margin:6px 0 16px"><?= htmlspecialchars($b['title']) ?></h1>
  <p style="line-height:1.6"><?= htmlspecialchars($b['hook']) ?></p>
  <h3 style="color:#f4a400;margin-bottom:6px">Objectives</h3>
  <ul style="line-height:1.7">
    <?php foreach ($b['objectives'] as $o): ?><li><?= htmlspecialchars($o) ?></li><?php endforeach; ?>
  </ul>
  <p style="line-height:1.6;color:#8aa0b4"><?= htmlspecialchars($b['note']) ?></p>
  <div style="margin-top:18px;padding:16px;background:#0c131b;border:1px solid #223042;border-radius:8px">
    <div style="color:#8aa0b4;font-size:12px;letter-spacing:1px;text-transform:uppercase">Stage 1 completion key</div>
    <code style="font-size:18px;color:#3ad29f"><?= completion_key($P) ?></code>
  </div>
</div>
<div class="panel">
  <a class="btn ghost" href="/challenges.php">&larr; Back to Stage 1</a>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
