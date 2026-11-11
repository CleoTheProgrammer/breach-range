<?php
require_once __DIR__ . '/db.php';
// Fixed: role-gated, no diagnostic/phpinfo exposure, and sensitive columns
// (api_token) are not rendered in the console.
if (($_SESSION['role'] ?? '') !== 'admin') { http_response_code(403); exit('Forbidden'); }
$users = db()->query("SELECT id,username,full_name,email,role FROM users")->fetchAll(PDO::FETCH_ASSOC);
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card">
  <h2 style="margin-top:0">Administration console</h2>
  <table><tr><th>ID</th><th>User</th><th>Name</th><th>Email</th><th>Role</th></tr>
  <?php foreach ($users as $u): ?>
    <tr><td><?= $u['id'] ?></td><td><?= htmlspecialchars($u['username']) ?></td>
        <td><?= htmlspecialchars($u['full_name']) ?></td><td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= htmlspecialchars($u['role']) ?></td></tr>
  <?php endforeach; ?></table>
  <p><a href="/netcheck.php">Network diagnostics tool</a></p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
