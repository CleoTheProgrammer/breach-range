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
$quiz = $c['quiz'];
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correct = 0;
    foreach ($quiz as $i => $qq) {
        $right = $qq['opts'][$qq['a']];
        if (($_POST["q$i"] ?? '') === $right) $correct++;
    }
    if ($correct === count($quiz)) {
        $up = db()->prepare(
            "INSERT INTO progress (player_id, vuln_id, flag_at, quiz_passed, quiz_at)
             VALUES (?,?,NOW(),1,NOW())
             ON DUPLICATE KEY UPDATE quiz_passed = 1, quiz_at = NOW()");
        $up->execute([$P['id'], $v]);
        $_SESSION['flash'] = ['ok', 'Passed, all correct (+' . PTS_QUIZ . ').'];
        header('Location: /challenges.php'); exit;
    }
    $result = $correct . ' of ' . count($quiz) . ' correct. This quiz needs a perfect score, review the debrief and try again.';
}

// Shuffle option order per render so the position of the answer is not a tell.
require_once __DIR__ . '/includes/header.php'; ?>
<div class="panel">
  <div style="color:#8aa0b4;font-size:12px;letter-spacing:2px;text-transform:uppercase">Quiz, <?= htmlspecialchars($c['title']) ?></div>
  <h2 style="margin:6px 0 4px">Prove it</h2>
  <p style="color:#8aa0b4;font-size:13px">All answers must be correct to score. <a href="/lesson.php?v=<?= $v ?>">Re-read the debrief</a>.</p>
  <?php if ($result): ?><div class="flash err"><?= htmlspecialchars($result) ?></div><?php endif; ?>
  <form method="post">
    <?php foreach ($quiz as $i => $qq): $opts = $qq['opts']; $order = array_keys($opts); shuffle($order); ?>
      <div style="margin:18px 0">
        <div style="font-weight:600;margin-bottom:8px"><?= ($i+1) . '. ' . htmlspecialchars($qq['q']) ?></div>
        <?php foreach ($order as $k): ?>
          <label style="display:block;color:#e6eef6;font-weight:400;cursor:pointer;margin:4px 0">
            <input type="radio" name="q<?= $i ?>" value="<?= htmlspecialchars($opts[$k]) ?>" style="width:auto;margin-right:8px" required>
            <?= htmlspecialchars($opts[$k]) ?>
          </label>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
    <button class="btn" type="submit">Submit answers</button>
  </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
