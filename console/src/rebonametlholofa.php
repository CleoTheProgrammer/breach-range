<?php
require_once __DIR__ . '/helpers.php';
// Instructor-only. Students never see the leaderboard. Unlock with the instructor
// password (set INSTRUCTOR_PASSWORD on the console service; change it from the default).
if (!empty($_GET['lock'])) { unset($_SESSION['instructor']); header('Location: /rebonametlholofa.php'); exit; }
if (empty($_SESSION['instructor'])) {
    $err = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (hash_equals(INSTRUCTOR_PASSWORD, $_POST['instructor_pw'] ?? '')) {
            $_SESSION['instructor'] = true;
            header('Location: /rebonametlholofa.php'); exit;
        }
        $err = 'Incorrect instructor password.';
    }
    require_once __DIR__ . '/includes/header.php'; ?>
    <div class="panel" style="max-width:420px">
      <h2 style="margin-top:0">Instructor access</h2>
      <p style="color:#8aa0b4;font-size:13px">This page is visible to the instructor only.</p>
      <?php if ($err): ?><div class="flash err"><?= htmlspecialchars($err) ?></div><?php endif; ?>
      <form method="post">
        <label>Instructor password</label>
        <input name="instructor_pw" type="password" autofocus>
        <button class="btn" type="submit">Unlock</button>
      </form>
    </div>
    <?php require_once __DIR__ . '/includes/footer.php'; exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_vis'])) {
    set_challenges_visible($_POST['set_vis'] === '1');
    header('Location: /rebonametlholofa.php'); exit;
}
$challenges_shown = challenges_visible();
$rows = leaderboard();
$total = count(challenges()) * (PTS_FLAG + PTS_QUIZ);
require_once __DIR__ . '/includes/header.php'; ?>
<div class="panel" style="<?= $challenges_shown ? '' : 'border-color:#f4a400' ?>">
  <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
    <div>
      <div style="color:#8aa0b4;font-size:12px;letter-spacing:2px;text-transform:uppercase">Challenge visibility</div>
      <h3 style="margin:4px 0">Challenges are <?= $challenges_shown ? 'SHOWN' : 'HIDDEN' ?> to students</h3>
    </div>
    <form method="post" style="margin-left:auto">
      <input type="hidden" name="set_vis" value="<?= $challenges_shown ? '0' : '1' ?>">
      <button class="btn" type="submit"><?= $challenges_shown ? 'Hide challenges' : 'Show challenges' ?></button>
    </form>
  </div>
  <p style="color:#8aa0b4;font-size:13px;margin:8px 0 0">Students' pages update within a few seconds, no refresh needed.</p>
</div>
<div class="panel">
  <div style="display:flex;align-items:center;gap:12px">
    <h2 style="margin:0">Rebonametlholofa</h2>
    <a href="/rebonametlholofa.php?lock=1" style="margin-left:auto;font-size:12px">lock</a>
  </div>
  <p style="color:#8aa0b4;font-size:13px">Instructor view. Ranked by flags captured; ties go to whoever got there first (time). Top 3 are the podium. <?= count(challenges()) ?> flags in total.</p>
  <table>
    <tr><th>#</th><th>Operator</th><th>Flags</th><th>Points</th><th>Last solve</th><th>Stage 1</th></tr>
    <?php foreach ($rows as $i => $r): ?>
      <tr style="<?= $i < 3 ? 'background:rgba(244,164,0,.08)' : '' ?>">
        <td><?php if ($i < 3): ?><b style="color:#f4a400"><?= $i+1 ?></b><?php else: ?><?= $i+1 ?><?php endif; ?></td>
        <td><b><?= htmlspecialchars($r['handle']) ?></b></td>
        <td style="color:#f4a400"><?= $r['flags'] ?></td>
        <td><?= $r['points'] ?></td>
        <td style="color:#8aa0b4;font-size:13px"><?= $r['last'] !== '' ? htmlspecialchars($r['last']) : '-' ?></td>
        <td><?php if ($r['cleared']): ?><span class="pill done">cleared</span><?php else: ?><span class="pill todo">in progress</span><?php endif; ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="6" style="color:#8aa0b4">No operators enlisted yet.</td></tr><?php endif; ?>
  </table>
</div>
<?php require_once __DIR__ . '/includes/footer.php';
