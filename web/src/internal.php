<?php
// Internal manifest service, reachable only from the app host itself and no longer
// holding secrets. The SSRF that used to reach it from outside is closed in fetch.php.
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($ip, ['127.0.0.1', '::1'], true)) { http_response_code(403); exit('Forbidden: internal only'); }
header('Content-Type: text/plain');
echo "Kestrel internal manifest service\nenv: production\n";
