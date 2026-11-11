<?php
// Internal manifest service, reachable only from the app host itself.
// Reached from outside via the SSRF in fetch.php.
require_once __DIR__ . '/flags.php';
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($ip, ['127.0.0.1', '::1'], true)) { http_response_code(403); exit('Forbidden: internal only'); }
header('Content-Type: text/plain');
echo "Kestrel internal manifest service\n";
echo "env: production\n";
echo pf('ssrf') . "\n";
