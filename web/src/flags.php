<?php
// Per-player flag derivation. The player's key arrives via the pk cookie
// (set by start.php), or the X-Player-Key header / ?pk= for API calls.
// The pepper is shared with the console and NEVER exposed to players, so a
// player cannot compute a flag without actually reaching the vulnerable sink.
function player_key() {
    $k = $_COOKIE['pk'] ?? ($_SERVER['HTTP_X_PLAYER_KEY'] ?? ($_GET['pk'] ?? ''));
    return preg_match('/^[a-f0-9]{16,64}$/', $k) ? $k : '';
}
function pf($vuln) {
    $pk = player_key();
    if ($pk === '') return 'FLAG{start_from_the_console_to_get_your_flag}';
    $pepper = getenv('FLAG_PEPPER') ?: 'dev-pepper';
    return 'FLAG{' . $vuln . '_' . substr(hash_hmac('sha256', $vuln . '|' . $pk, $pepper), 0, 12) . '}';
}
