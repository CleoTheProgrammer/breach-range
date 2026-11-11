<?php
require_once __DIR__ . '/db.php';
// A03 Injection (OS command injection). Admin "network diagnostics" tool passes the
// host straight into a shell. e.g.  host=127.0.0.1; cat /flag_rce.txt
// or host=127.0.0.1; id  -> full command execution as www-data.
if (($_SESSION['role'] ?? '') !== 'admin') { http_response_code(403); exit('Forbidden'); }
$host = $_GET['host'] ?? '';
$output = '';
if ($host !== '') {
    $output = shell_exec('ping -c 2 ' . $host . ' 2>&1');   // unsanitised concatenation
}
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card">
  <h2 style="margin-top:0">Network diagnostics</h2>
  <p>Check connectivity from the portal host to a partner endpoint.</p>
  <form method="get">
    <label>Host or IP</label><input name="host" value="<?= htmlspecialchars($host) ?>" placeholder="127.0.0.1">
    <button class="btn amber" type="submit">Ping</button>
  </form>
  <?php if ($host !== ''): ?>
    <pre style="background:#0d1b26;color:#d6e6f2;padding:14px;border-radius:6px;overflow:auto"><?= htmlspecialchars($output) ?></pre>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
