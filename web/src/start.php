<?php
// Handshake from the console: binds this browser to a player key, then writes
// that player's RCE flag to a file only reachable via code execution.
require_once __DIR__ . '/flags.php';
$pk = $_GET['pk'] ?? '';
if (!preg_match('/^[a-f0-9]{16,64}$/', $pk)) { http_response_code(400); exit('invalid or missing player key'); }
setcookie('pk', $pk, ['path' => '/', 'httponly' => false, 'samesite' => 'Lax']);
$_COOKIE['pk'] = $pk; // so pf() works within this request
@mkdir('/var/flags', 0777, true);
@file_put_contents('/var/flags/' . $pk . '.flag', pf('rce') . "\n");
header('Location: /index.php');
exit;
