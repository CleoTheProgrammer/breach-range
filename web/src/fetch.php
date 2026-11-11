<?php
require_once __DIR__ . '/db.php';
if (empty($_SESSION['uid'])) { header('Location: /login.php'); exit; }
// A10 SSRF: "import from partner URL" fetches any URL server-side with no allow-list.
// Reaches internal-only endpoints (e.g. http://127.0.0.1/internal.php) and the db host.
$body = ''; $url = $_GET['url'] ?? '';
if ($url !== '') {
    $ctx = stream_context_create(['http' => ['timeout' => 4]]);
    $body = @file_get_contents($url, false, $ctx);
    if ($body === false) { $body = '[fetch failed]'; }
}
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
