# Breach Range, Stage 1 ("Last Mile")

A deliberately vulnerable training range in the lineage of DVWA / OWASP Juice Shop, wrapped in a
real scoring + learning platform. You attack a fictional logistics company, **Kestrel Freight
Solutions**, capture a flag for each weakness, then learn the risk, the business impact and the
fix, and prove it on a quiz. Covers the **OWASP Web Top 10 (2021)** and **OWASP API Top 10 (2023)**.

> **Isolated lab only.** The target is intentionally insecure. Never put it on a public or shared
> network. Ports are bound to 127.0.0.1. Run it on a disposable VM behind NAT.

## Three services
| Service | URL | Role |
|---------|-----|------|
| **Console** | http://127.0.0.1:8081 | Secure. Accounts, flags, leaderboard, lessons, quizzes. Start here. |
| **Target** (Kestrel) | http://127.0.0.1:8080 | Deliberately vulnerable portal + API. What you attack. |
| **db** | internal | One MySQL, two access-scoped databases, so the target can never read the console's data. |

## How a player moves through it
1. Open the **console** (:8081), read the Stage 1 briefing, hit **Go** and enlist a handle.
2. Hit **Launch target**. That binds your personal key to the target so your flags are unique to you.
3. Attack the target. Each weakness reveals a FLAG only you can get.
4. **Submit** the flag in the console, it unlocks that vuln's risk + business impact + remediation.
5. Pass the **hard quiz** for the rest of the points. Climb the **leaderboard**.

Flags are FLAG{vuln_HMAC(your_key + secret_pepper)}, so they cannot be shared or googled.
Submitting someone else's flag is detected and rejected.

## Run it (Docker)
Use the helper so it prompts you for host ports (defaults 8080/8081), handy when a port is taken:
```bash
./scripts/run.sh up
```
Or run compose directly, overriding ports as needed:
```bash
TARGET_PORT=8080 CONSOLE_PORT=8081 docker compose up --build -d
```
Console is the console port (start here), target is the target port, both on 127.0.0.1.
Tear down and wipe data: `docker compose down -v`.

Target seed logins (for the scenario): tshepo:123456789, lerato:letmein, admin:admin.

## Workflow: GitLab + Ubuntu VM
```bash
git add -A && git commit -m "Stage 1 breach range + console"
git remote add origin git@gitlab.com:<you>/breach-range.git
git push -u origin main
```
On the Ubuntu VM:
```bash
sudo apt install -y docker.io docker-compose-plugin
git clone git@gitlab.com:<you>/breach-range.git && cd breach-range && docker compose up --build -d
```

## Notes
- **Change FLAG_PEPPER** in docker-compose.yml (same value for target and console) before a real
  event, so flags are specific to your deployment.
- The payloads/ webshell and reverse shell may be flagged by antivirus on a Windows checkout, that
  is a false positive on intentional lab tooling; they are fine on the Linux VM.
- Instructor answer key: docs/VULN-MAP.md. Narrative + design: docs/STORYLINE.md.

## Layout
```
web/       the target (PHP + Apache portal and API)
console/   the scoring + learning platform (secure PHP app)
db/        00-init.sql (target, scoped users) + 10-console.sql (platform schema)
payloads/  webshell + reverse shell you upload during the RCE challenge
docs/      STORYLINE.md, VULN-MAP.md
scripts/   run.sh helper
docker-compose.yml, .gitlab-ci.yml
```

## Hosting it for a class (network-reachable)
The services now publish on all interfaces, so students can reach them by the VM's IP and
`nmap` the open ports. Run only on an **isolated lab / classroom network**, it is intentionally
vulnerable.

1. In VirtualBox, give the VM a **Bridged** (or Host-only) adapter so it has a LAN IP students can reach.
2. Bring it up and let the helper detect the IP and print what to hand out:
```bash
./scripts/run.sh up
```
   It prompts for the ports and the public IP (defaults to the detected one), then prints the
   student URLs and the `nmap` target.
3. If `ufw` is active on the VM, open the ports (the script reminds you):
```bash
sudo ufw allow 8080/tcp && sudo ufw allow 8081/tcp
```
4. Give students the IP. `nmap <ip>` shows the ports; the **target** is what they attack, the
   **console** is where they submit flags.

Tip: set the target to port 80 (`Target port: 80` at the prompt) for a more realistic scan.

### Rebonametlholofa (the leaderboard) is instructor-only
Students never see the Rebonametlholofa page, its nav link is hidden and it is password-gated.
Set your password on the console service and keep it to yourself:
```bash
INSTRUCTOR_PASSWORD='your-secret' ./scripts/run.sh up
```
Then open `http://<ip>:<console-port>/rebonametlholofa.php`, enter the password once, and use the
`lock` link to drop access again. Change `INSTRUCTOR_PASSWORD` from its default before hosting.

### Audit log, non-repudiation (instructor-only)
Every scoring action (register, login, blocked-handle attempt, flag submit and result, quiz pass,
hint use, and instructor actions) is written to an append-only audit log, each entry HMAC-chained to
the previous one with `AUDIT_SECRET`. Any edit or deletion breaks the chain and is flagged, and the
chain cannot be forged without the secret. View it at `/tlareboneodirileng.php` (instructor-only, shown as "Tlareboneodirileng"
in the nav once unlocked); the top banner says whether the chain is intact or where it was tampered.
The audit table is created at DB init, so if you added this to an existing instance, recreate the DB
with `docker compose down -v` once. Set a real `AUDIT_SECRET` and keep it private.

### Env vars
`TARGET_PORT`, `CONSOLE_PORT` (host ports), `PUBLIC_HOST` (IP in the console's launch links),
`BIND_HOST` (default `0.0.0.0`; set `127.0.0.1` to go back to local-only), `INSTRUCTOR_PASSWORD`,
`AUDIT_SECRET` (keys the tamper-evident audit chain; change it),
`FLAG_PEPPER` (change before an event).
