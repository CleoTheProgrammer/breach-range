<?php
// Internal deploy/config service with a debug endpoint that leaks secrets.
function pf($v){ $pk=$_SERVER['HTTP_X_PLAYER_KEY']??($_GET['pk']??''); if(!preg_match('/^[a-f0-9]{16,64}$/',$pk)) return 'FLAG{send_your_player_key_as_header_X-Player-Key_or_?pk=}'; return 'FLAG{'.$v.'_'.substr(hash_hmac('sha256',$v.'|'.$pk,getenv('FLAG_PEPPER')?:'dev-pepper'),0,12).'}'; }
header('Content-Type: text/plain');
if (!empty($_GET['debug'])) {
    echo "Kestrel Deploy Service - DEBUG MODE\n\nENVIRONMENT:\n";
    echo " APP_ENV=production\n";
    echo " AWS_ACCESS_KEY_ID=AKIAKESTRELDEMO0001\n";
    echo " AWS_SECRET_ACCESS_KEY=wJalrXUtnFEMI/K7MDENG/bPxRfiCYKESTRELdemo\n";
    echo " AWS_REGION=af-south-1\n\n";
    echo "FLAG: ".pf('s2_secrets')."\n";
} else {
    echo "Kestrel Deploy Service. Set ?debug=1 for diagnostics.\n";
}
