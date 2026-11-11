<?php
require_once __DIR__ . '/helpers.php';
$P = require_player();
$C = challenges();
$v = $_GET['v'] ?? '';
if (!isset($C[$v])) { header('Location: /challenges.php'); exit; }
$prog = progress_for($P['id']);
if (empty($prog[$v]['flag'])) {
    $_SESSION['flash'] = ['err', 'Capture that flag on the target first.'];
    header('Location: /challenges.php'); exit;
}
$c = $C[$v];
$done = !empty($prog[$v]['quiz']);
require_once __DIR__ . '/includes/header.php'; ?>

<div class="panel">
  <div style="color:#8aa0b4;font-size:12px;letter-spacing:2px;text-transform:uppercase"><?= htmlspecialchars($c['owasp']) ?></div>
  <h1 style="margin:6px 0 18px"><?= htmlspecialchars($c['title']) ?></h1>

  <h3 style="color:#f4a400;margin-bottom:4px">What happened</h3>
  <p style="line-height:1.6"><?= htmlspecialchars($c['what']) ?></p>

  <h3 style="color:#f4a400;margin-bottom:4px">Why it matters, the business impact</h3>
  <p style="line-height:1.6"><?= htmlspecialchars($c['impact']) ?></p>

  <h3 style="color:#f4a400;margin-bottom:4px">How to remediate</h3>
  <p style="line-height:1.6"><?= htmlspecialchars($c['fix']) ?></p>

  <div style="margin-top:22px">
    <?php if ($done): ?>
      <span class="pill done">Quiz passed</span>
      <a class="btn ghost" href="/challenges.php" style="margin-left:10px">Back to challenges</a>
    <?php else: ?>
      <a class="btn" href="/quiz.php?v=<?= $v ?>">Take the quiz (+<?= PTS_QUIZ ?>) &rarr;</a>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
