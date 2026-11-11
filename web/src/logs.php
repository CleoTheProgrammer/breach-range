<?php
// A09 Logging failures: a debug log viewer left exposed in production, and worse,
// the app logs sensitive values in cleartext. Disclosed via robots.txt.
require_once __DIR__ . '/flags.php';
header('Content-Type: text/plain');
$pk = player_key();
echo "[app] 08:14:02 INFO  auth.login user=tshepo result=ok\n";
echo "[app] 08:14:05 INFO  shipment.view id=100001\n";
echo "[app] 08:15:31 DEBUG payment.gateway apikey=sk_live_51KpQ...redacted\n";
echo "[app] 08:16:10 INFO  auth.login user=admin result=ok ip=10.0.0.4\n";
if ($pk !== '') {
    echo "[app] 08:16:11 DEBUG session.token issued=" . pf('logging') . "\n";
}
echo "[app] 08:17:44 WARN  reset.request user=lerato\n";
