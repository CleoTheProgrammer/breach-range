<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/flags.php';
// A01: only a role check, reachable by anyone who escalated via profile mass assignment.
if (($_SESSION['role'] ?? '') !== 'admin') { http_response_code(403); exit('Forbidden'); }
$users = q_raw("SELECT id,username,full_name,email,role,api_token FROM users")->fetchAll(PDO::FETCH_ASSOC);
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card">
  <h2 style="margin-top:0">Administration console</h2>
  <p style="color:#2e7d32">Admin access confirmed. Your flag: <b><?= pf('privesc') ?></b></p>
  <table><tr><th>ID</th><th>User</th><th>Name</th><th>Email</th><th>Role</th><th>API token</th></tr>
  <?php foreach ($users as $u): ?>
    <tr><td><?= $u['id'] ?></td><td><?= htmlspecialchars($u['username']) ?></td>
        <td><?= htmlspecialchars($u['full_name']) ?></td><td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= htmlspecialchars($u['role']) ?></td><td><code><?= htmlspecialchars($u['api_token']) ?></code></td></tr>
  <?php endforeach; ?></table>
  <p><a href="/admin.php?diag=1">Server diagnostics</a></p>
  <p><a href="/netcheck.php">Network diagnostics tool</a></p>
  <?php if (!empty($_GET['diag'])) { echo '<p style="color:#2e7d32">Config exposed. Your flag: <b>'.pf('misconfig').'</b></p>'; phpinfo(); } ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
