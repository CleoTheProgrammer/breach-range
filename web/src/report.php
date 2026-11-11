<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/flags.php';
if (empty($_SESSION['uid'])) { header('Location: /login.php'); exit; }
// A06 Vulnerable/outdated component: a legacy template helper (SimpleTpl v1.0,
// unpatched) renders {{ expr }} with eval(). Server-side template injection via a
// dependency the team never updated. Try {{ 7*7 }} then {{ phpversion() }}.
$tpl = $_GET['tpl'] ?? '';
$rendered = ''; $earned = '';
if ($tpl !== '') {
    $rendered = preg_replace_callback('/\{\{(.+?)\}\}/', function ($m) {
        try { return (string)@eval('return ' . $m[1] . ';'); } catch (\Throwable $e) { return '[err]'; }
    }, $tpl);
    if ($rendered !== $tpl) { $earned = pf('components'); }
}
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card">
  <h2 style="margin-top:0">Shipment report builder</h2>
  <p style="font-size:13px;color:#8296a8">Powered by SimpleTpl v1.0. Use <code>{{ field }}</code> placeholders.</p>
  <form method="get">
    <label>Template</label><input name="tpl" value="<?= htmlspecialchars($tpl) ?>" placeholder="Report for {{ date('Y') }}">
    <button class="btn amber" type="submit">Render</button>
  </form>
  <?php if ($tpl !== ''): ?>
    <h4>Preview</h4><pre style="background:#0d1b26;color:#d6e6f2;padding:14px;border-radius:6px;overflow:auto"><?= htmlspecialchars($rendered) ?></pre>
    <?php if ($earned): ?><p style="color:#2e7d32">Template engine executed your code. Your flag: <b><?= htmlspecialchars($earned) ?></b></p><?php endif; ?>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
