<?php
require_once __DIR__ . '/config.php';
// Identical derivation to the target's pf(). Given a player's key we can
// recompute the exact flag they should capture for each vuln, and validate.
function cflag($key, $vuln) {
    return 'FLAG{' . $vuln . '_' . substr(hash_hmac('sha256', $vuln . '|' . $key, FLAG_PEPPER), 0, 12) . '}';
}
