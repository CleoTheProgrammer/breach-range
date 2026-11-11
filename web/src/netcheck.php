<?php
require_once __DIR__ . '/db.php';
// Fixed: admin only, strict validation that the input is an IP or hostname, and
// the value passed as a single escaped argument. No shell metacharacters survive.
if (($_SESSION['role'] ?? '') !== 'admin') { http_response_code(403); exit('Forbidden'); }
$host = $_GET['host'] ?? '';
$output = ''; $err = '';
if ($host !== '') {
    $isIp   = filter_var($host, FILTER_VALIDATE_IP) !== false;
    $isHost = preg_match('/^(?=.{1,253}$)([a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/i', $host) === 1;
    if ($isIp || $isHost) {
        $output = shell_exec('ping -c 2 ' . escapeshellarg($host) . ' 2>&1');
    } else {
        $err = 'Enter a valid IP address or hostname.';
    }
}
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card">
  <h2 style="margin-top:0">Network diagnostics</h2>
  <form method="get">
    <label>Host or IP</label><input name="host" value="<?= htmlspecialchars($host) ?>" placeholder="127.0.0.1">
    <button class="btn amber" type="submit">Ping</button>
  </form>
  <?php if ($err): ?><p style="color:#c0392b"><?= htmlspecialchars($err) ?></p><?php endif; ?>
  <?php if ($output !== ''): ?><pre style="background:#0d1b26;color:#d6e6f2;padding:14px;border-radius:6px;overflow:auto"><?= htmlspecialchars($output) ?></pre><?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
