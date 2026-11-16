<?php
require_once __DIR__ . '/db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /index.php'); exit; }

// Suggest a free handle based on what they tried.
function suggest_handle($base) {
    $base = preg_replace('/\s+/', '', $base);
    if ($base === '') $base = 'player';
    for ($i = 0; $i < 10; $i++) {
        $cand = substr($base, 0, 36) . random_int(2, 999);
        $s = db()->prepare("SELECT 1 FROM players WHERE handle = ?"); $s->execute([$cand]);
        if (!$s->fetch()) return $cand;
    }
    return substr($base, 0, 34) . '-' . substr(bin2hex(random_bytes(2)), 0, 4);
}

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
    // Correct passphrase = the owner resuming their own session.
    if (password_verify($pw, $p['pass_hash'])) {
        $_SESSION['pid'] = $p['id'];
        header('Location: /challenges.php'); exit;
    }
    // Otherwise the name is taken: block it and suggest a free one, let them retry.
    $suggestion = suggest_handle($handle);
    $_SESSION['suggested_handle'] = $suggestion;
    $_SESSION['flash'] = ['err', 'You cannot use this username, it is already taken. Try "' . $suggestion . '", or choose another.'];
    header('Location: /index.php'); exit;
}

// New handle: register.
$key = bin2hex(random_bytes(16));
$ins = db()->prepare("INSERT INTO players (handle, pass_hash, player_key) VALUES (?,?,?)");
$ins->execute([$handle, password_hash($pw, PASSWORD_DEFAULT), $key]);
$_SESSION['pid'] = db()->lastInsertId();
$_SESSION['flash'] = ['ok', 'Operator ' . $handle . ' enlisted. Launch the target and start hunting.'];
header('Location: /challenges.php');
exit;
