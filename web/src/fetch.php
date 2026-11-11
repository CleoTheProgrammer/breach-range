<?php
require_once __DIR__ . '/db.php';
if (empty($_SESSION['uid'])) { header('Location: /login.php'); exit; }
// Fixed SSRF: only http(s), resolve the host and refuse private, loopback,
// link-local and reserved ranges, and do not follow redirects.
function safe_fetch($url) {
    $p = parse_url($url);
    if (!$p || !in_array(strtolower($p['scheme'] ?? ''), ['http','https'], true) || empty($p['host'])) {
        return '[rejected: only http(s) URLs are allowed]';
    }
    $ips = @gethostbynamel($p['host']);
    if (!$ips) return '[rejected: host did not resolve]';
    foreach ($ips as $ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP,
              FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return '[rejected: destination resolves to a private or reserved address]';
        }
    }
    $ctx = stream_context_create(['http' => ['timeout' => 4, 'max_redirects' => 0, 'follow_location' => 0]]);
    $body = @file_get_contents($url, false, $ctx);
    return $body === false ? '[fetch failed]' : $body;
}
$body = ''; $url = $_GET['url'] ?? '';
if ($url !== '') { $body = safe_fetch($url); }
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card">
  <h2 style="margin-top:0">Import partner manifest</h2>
  <form method="get">
    <label>Manifest URL</label><input name="url" value="<?= htmlspecialchars($url) ?>" placeholder="https://partner.example/manifest.json">
    <button class="btn amber" type="submit">Fetch</button>
  </form>
  <?php if ($url !== ''): ?>
    <h4>Response</h4><pre style="background:#0d1b26;color:#d6e6f2;padding:14px;border-radius:6px;overflow:auto"><?= htmlspecialchars($body) ?></pre>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
