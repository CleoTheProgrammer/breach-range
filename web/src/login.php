<?php
require_once __DIR__ . '/db.php';
// Fixed: parameterised query, generic failure (no enumeration), password_verify
// with MD5->bcrypt upgrade, AND rate limiting / lockout against brute force (A07).
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $now = time();
    $_SESSION['login_fail'] = array_values(array_filter($_SESSION['login_fail'] ?? [],
        fn($t) => $t > $now - 900));                       // 15-minute window
    if (count($_SESSION['login_fail']) >= 5) {
        $err = 'Too many failed attempts. Please wait before trying again.';
    } else {
        $u = $_POST['username'] ?? '';
        $p = $_POST['password'] ?? '';
        $stmt = db()->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$u]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $ok = false;
        if ($row) {
            $h = $row['password'];
            if (preg_match('/^[a-f0-9]{32}$/i', $h)) {
                if (hash_equals(strtolower($h), md5($p))) {
                    $ok = true;
                    db()->prepare("UPDATE users SET password = ? WHERE id = ?")
                        ->execute([password_hash($p, PASSWORD_DEFAULT), $row['id']]);
                }
            } else {
                $ok = password_verify($p, $h);
            }
        }
        if ($ok) {
            unset($_SESSION['login_fail']);
            session_regenerate_id(true);
            $_SESSION['uid']  = $row['id'];
            $_SESSION['uname']= $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['name'] = $row['full_name'];
            header('Location: /dashboard.php'); exit;
        }
        $_SESSION['login_fail'][] = $now;
        $err = 'Invalid username or password.';
    }
}
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card" style="max-width:420px;margin:0 auto">
  <h2 style="margin-top:0">Sign in</h2>
  <?php if ($err): ?><p style="color:#c0392b"><?= htmlspecialchars($err) ?></p><?php endif; ?>
  <form method="post">
    <label>Username</label><input name="username" autofocus>
    <label>Password</label><input name="password" type="password">
    <button class="btn amber" type="submit">Sign in</button>
  </form>
  <p style="font-size:12px;color:#8296a8"><a href="/reset.php">Forgot password?</a></p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
