<?php
require_once __DIR__ . '/helpers.php';
// Instructor-only. Students never see the leaderboard. Unlock with the instructor
// password (set INSTRUCTOR_PASSWORD on the console service; change it from the default).
if (!empty($_GET['lock'])) { unset($_SESSION['instructor']); header('Location: /leaderboard.php'); exit; }
if (empty($_SESSION['instructor'])) {
    $err = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (hash_equals(INSTRUCTOR_PASSWORD, $_POST['instructor_pw'] ?? '')) {
            $_SESSION['instructor'] = true;
            header('Location: /leaderboard.php'); exit;
        }
        $err = 'Incorrect instructor password.';
    }
    require_once __DIR__ . '/includes/header.php'; ?>
    <div class="panel" style="max-width:420px">
      <h2 style="margin-top:0">Instructor access</h2>
      <p style="color:#8aa0b4;font-size:13px">The leaderboard is visible to the instructor only.</p>
      <?php if ($err): ?><div class="flash err"><?= htmlspecialchars($err) ?></div><?php endif; ?>
      <form method="post">
        <label>Instructor password</label>
        <input name="instructor_pw" type="password" autofocus>
        <button class="btn" type="submit">Unlock</button>
      </form>
    </div>
    <?php require_once __DIR__ . '/includes/footer.php'; exit;
}
$rows = leaderboard();
$total = count(challenges()) * (PTS_FLAG + PTS_QUIZ);
require_once __DIR__ . '/includes/header.php'; ?>
<div class="panel">
  <div style="display:flex;align-items:center;gap:12px">
    <h2 style="margin:0">Leaderboard</h2>
    <a href="/leaderboard.php?lock=1" style="margin-left:auto;font-size:12px">lock</a>
  </div>
  <p style="color:#8aa0b4;font-size:13px">Instructor view. Ranked by points, ties broken by who got there first. Max possible: <b><?= $total ?></b> (hints cost points).</p>
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
<?php require_once __DIR__ . '/includes/footer.php';
