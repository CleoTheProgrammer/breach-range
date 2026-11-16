<?php
// Internal staff intranet. Reachable only from inside the corporate network.
function pf($v){ $pk=$_SERVER['HTTP_X_PLAYER_KEY']??($_GET['pk']??''); if(!preg_match('/^[a-f0-9]{16,64}$/',$pk)) return 'FLAG{send_your_player_key_as_header_X-Player-Key_or_?pk=}'; return 'FLAG{'.$v.'_'.substr(hash_hmac('sha256',$v.'|'.$pk,getenv('FLAG_PEPPER')?:'dev-pepper'),0,12).'}'; }
header('Content-Type: text/plain');
echo "Kestrel Staff Intranet (internal)\n";
echo "Internal use only. No login required on the corporate LAN.\n\n";
echo "Employee directory:\n";
echo " - Tshepo Mahlangu  | Logistics | ext 204\n";
echo " - Lerato Dube      | Finance   | ext 311\n";
echo " - Sipho Ndlovu     | Driver    | ext 150\n\n";
echo "Other internal systems: finance portal http://finance/ , deploy console http://secrets/\n";
echo "FLAG: ".pf('s2_intranet')."\n";
