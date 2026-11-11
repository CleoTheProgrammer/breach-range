<?php
require_once __DIR__ . '/db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /index.php'); exit; }
$handle = trim($_POST['handle'] ?? '');
$pw     = $_POST['password'] ?? '';
if ($handle === '' || $pw === '' || !preg_match('/^[A-Za-z0-9_\- ]{2,40}$/', $handle)) {
    $_SESSION['flash'] = ['err', 'Pick a handle (2-40 chars) and a passphrase.'];
    header('Location: /index.php'); exit;
}
$s = db()->prepare("SELECT * FROM players WHERE handle = ?");
$s->execute([$handle]);
$p = $s->fetch(PDO::FETCH_ASSOC);

if ($p) {
    if (!password_verify($pw, $p['pass_hash'])) {
        $_SESSION['flash'] = ['err', 'That handle exists but the passphrase is wrong.'];
        header('Location: /index.php'); exit;
    }
    $_SESSION['pid'] = $p['id'];
} else {
    $key = bin2hex(random_bytes(16)); // per-player secret
    $ins = db()->prepare("INSERT INTO players (handle, pass_hash, player_key) VALUES (?,?,?)");
    $ins->execute([$handle, password_hash($pw, PASSWORD_DEFAULT), $key]);
    $_SESSION['pid'] = db()->lastInsertId();
    $_SESSION['flash'] = ['ok', 'Operator ' . $handle . ' enlisted. Launch the target and start hunting.'];
}
header('Location: /challenges.php');
exit;
