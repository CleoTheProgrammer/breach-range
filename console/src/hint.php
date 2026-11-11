<?php
require_once __DIR__ . '/helpers.php';
$P = require_player();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /challenges.php'); exit; }
$v = $_POST['v'] ?? '';
$H = hints();
if (!isset($H[$v])) { header('Location: /challenges.php'); exit; }

$prog = progress_for($P['id']);
if (!empty($prog[$v]['flag'])) {
    $_SESSION['flash'] = ['ok', 'You already captured that one, no hint needed.'];
    header('Location: /challenges.php'); exit;
}
// Mark hint used (idempotent) so the cost applies once.
$up = db()->prepare(
    "INSERT INTO progress (player_id, vuln_id, hint_used) VALUES (?,?,1)
     ON DUPLICATE KEY UPDATE hint_used = 1");
$up->execute([$P['id'], $v]);
$_SESSION['flash'] = ['ok', 'Hint (-' . HINT_COST . '): ' . $H[$v]];
header('Location: /challenges.php');
exit;
