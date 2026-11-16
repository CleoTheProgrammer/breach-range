<?php
// Internal finance portal. Accepts the same password that leaked on the perimeter.
function pf($v){ $pk=$_SERVER['HTTP_X_PLAYER_KEY']??($_GET['pk']??''); if(!preg_match('/^[a-f0-9]{16,64}$/',$pk)) return 'FLAG{send_your_player_key_as_header_X-Player-Key_or_?pk=}'; return 'FLAG{'.$v.'_'.substr(hash_hmac('sha256',$v.'|'.$pk,getenv('FLAG_PEPPER')?:'dev-pepper'),0,12).'}'; }
header('Content-Type: text/plain');
$pw = $_REQUEST['pw'] ?? '';
echo "Kestrel Finance Portal (internal)\n\n";
if ($pw === 'Summer2023!') {
    echo "Authenticated, credential accepted.\n\nOutstanding invoices:\n";
    echo " INV-1001  Acme Corp     R 98,000  acct 62xxxx4471\n";
    echo " INV-1002  Blue Freight  R 45,200  acct 10xxxx8890\n\n";
    echo "FLAG: ".pf('s2_credreuse')."\n";
} else {
    echo "Access denied. Supply the finance password:  ?pw=...\n";
}
