<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/flags.php';
// A03 Injection (SQLi) + A07 Auth failures. On a detected auth-bypass we award
// the per-player sqli flag (we re-check the creds safely; if the vulnerable
// query let someone in without valid creds, it was an injection).
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $sql = "SELECT * FROM users WHERE username = '$u' AND password = MD5('$p')"; // vulnerable
    try {
        $row = q_raw($sql)->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            // Safe re-check of the literal credentials supplied.
            $chk = db()->prepare("SELECT id FROM users WHERE username = ? AND password = MD5(?)");
            $chk->execute([$u, $p]);
            if (!$chk->fetch()) { $_SESSION['sqli_flag'] = pf('sqli'); } // creds did not really match -> injection
            $_SESSION['uid']   = $row['id'];
            $_SESSION['uname'] = $row['username'];
            $_SESSION['role']  = $row['role'];
            $_SESSION['name']  = $row['full_name'];
            header('Location: /dashboard.php'); exit;
        } else {
            $exists = q_raw("SELECT id FROM users WHERE username = '$u'")->fetch();
            $err = $exists ? 'Incorrect password for that account.' : 'No such username.';
        }
    } catch (Exception $e) { $err = 'Query error: ' . $e->getMessage(); }
}
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card" style="max-width:420px;margin:0 auto">
  <h2 style="margin-top:0">Sign in</h2>
  <?php if ($err): ?><p style="color:#c0392b"><?= $err ?></p><?php endif; ?>
  <form method="post">
    <label>Username</label><input name="username" autofocus>
    <label>Password</label><input name="password" type="password">
    <button class="btn amber" type="submit">Sign in</button>
  </form>
  <p style="font-size:12px;color:#8296a8"><a href="/reset.php">Forgot password?</a></p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
