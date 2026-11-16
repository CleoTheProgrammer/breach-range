<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/flags.php';
$P = require_player();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /challenges.php'); exit; }

$submitted = trim($_POST['flag'] ?? '');
$match = null;
foreach (array_keys(challenges()) as $vuln) {
    if (hash_equals(cflag($P['player_key'], $vuln), $submitted)) { $match = $vuln; break; }
}

if ($match === null) {
    // Is it a valid flag but for another player? Give a pointed hint.
    if (preg_match('/^FLAG\{[a-z]+_[0-9a-f]{12}\}$/', $submitted)) {
        $_SESSION['flash'] = ['err', 'That is a flag, but not yours. Flags are unique per player, capture your own on the target.'];
    } else {
        $_SESSION['flash'] = ['err', 'Not a valid flag.'];
    }
    audit('flag_reject', 'submitted=' . substr($submitted, 0, 60));
    header('Location: /challenges.php'); exit;
}

$prog = progress_for($P['id']);
if (!empty($prog[$match]['flag'])) {
    $_SESSION['flash'] = ['ok', 'Already captured. Here is the debrief again.'];
} else {
    $up = db()->prepare(
        "INSERT INTO progress (player_id, vuln_id, flag_at) VALUES (?,?,NOW())
         ON DUPLICATE KEY UPDATE flag_at = NOW()");
    $up->execute([$P['id'], $match]);
    audit('flag_capture', 'vuln=' . $match);
    $_SESSION['flash'] = ['ok', 'Flag accepted (+' . PTS_FLAG . '). Now the debrief.'];
}
header('Location: /lesson.php?v=' . $match);
exit;
