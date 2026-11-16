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
