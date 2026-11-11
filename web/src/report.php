<?php
require_once __DIR__ . '/db.php';
if (empty($_SESSION['uid'])) { header('Location: /login.php'); exit; }
// Fixed: no code execution. The template engine substitutes only a fixed allow-list
// of tokens and never evaluates user input. Output is encoded.
$tpl = $_GET['tpl'] ?? '';
$map = ['{{year}}' => date('Y'), '{{company}}' => 'Kestrel Freight', '{{user}}' => ($_SESSION['name'] ?? '')];
$rendered = $tpl === '' ? '' : strtr($tpl, $map);
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card">
  <h2 style="margin-top:0">Shipment report builder</h2>
  <p style="font-size:13px;color:#8296a8">Allowed tokens: <code>{{year}}</code> <code>{{company}}</code> <code>{{user}}</code>.</p>
  <form method="get">
    <label>Template</label><input name="tpl" value="<?= htmlspecialchars($tpl) ?>" placeholder="Report for {{year}}">
    <button class="btn amber" type="submit">Render</button>
  </form>
  <?php if ($tpl !== ''): ?>
    <h4>Preview</h4><pre style="background:#0d1b26;color:#d6e6f2;padding:14px;border-radius:6px;overflow:auto"><?= htmlspecialchars($rendered) ?></pre>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
