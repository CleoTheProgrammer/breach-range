# Stage 1, "Last Mile"

## The story
Kestrel Freight Solutions is a regional logistics firm. When a competitor launched a slick
digital portal, Kestrel's board panicked and gave their small dev team eight weeks to ship a
customer portal and a partner API. They made the deadline. They did not make it safe.

You are contracted to assess the perimeter before a bigger firm acquires Kestrel and inherits
the risk. Stage 1 is everything an anonymous attacker can reach from the internet: the customer
portal and the partner API. Your objectives:

1. Get in without credentials.
2. Move from a customer account to administrator.
3. Reach something that was never meant to face the internet.
4. Find what the team forgot to switch off.

Four flags. Pull all four and the Stage 2 brief (the corporate network behind the portal)
unlocks.

## The intended path (one of several)
1. **Recon.** Read the home page source, note `v0.9.2 beta`, the leaked DB password in a TODO
   comment, directory listings, and `phpinfo` once you are admin.
2. **Foothold.** SQL-inject the login (`' OR '1'='1' -- `) or reset a password with the
   guessable 4-digit code. You are now a customer.
3. **Horizontal.** IDOR through `shipment.php?id=` until `id=42` leaks **flag 1**.
4. **Vertical.** POST `role=admin` to `profile.php` (mass assignment). Load `admin.php` for
   **flag 2** and every user's API token.
5. **Internal.** Use the manifest importer (`fetch.php`) to SSRF `http://127.0.0.1/internal.php`
   for **flag 3**.
6. **Code execution.** Upload `payloads/shell.php` through the photo upload and browse to
   `/uploads/shell.php?cmd=cat /flag_rce.txt`, or use the admin Network diagnostics tool
   (`netcheck.php?host=127.0.0.1; cat /flag_rce.txt`) for the **RCE flag**. Swap in
   `revshell.php` with a netcat listener for a full interactive shell.
7. **Forgotten.** Hit `/api/v0/` with no auth for **flag 4**.

## Make it fun
- **Flag format `FLAG{...}`** so it is CTF-muscle-memory and easy to grep/score.
- **Progressive unlock.** Each stage's brief is encrypted; the four Stage 1 flags concatenated
  are the key to decrypt `stage2.brief.enc`. Players cannot skip ahead.
- **Scoreboard.** A tiny scorer (planned `scripts/score.sh`) takes submitted flags and prints
  rank + time. Drop it in a shared channel for friendly competition.
- **Red herrings.** `KF-100002` notes say "signed by reception", the degraded invoice service
  hints at a service that does not exist yet. Teaches players to not chase everything.
- **Difficulty dials.** An `EASY_MODE` env could re-enable hints in HTML comments; default off.
- **Blue-team twist.** Later, ship a "hardened" branch where each vuln is fixed, and have
  players diff the two to learn remediation, not just exploitation.
- **Lore breadcrumbs.** Internal notes, ticket threads and employee names build a world, so it
  reads like a real breach, not a worksheet.

## The platform (built)
Stage 1 now runs as a platform, not just a target:
- **Console** (`:8081`): storyline briefing with a **Go** button, operator enlistment, a personal
  target launch link, a flag submission box, per-vuln debriefs (what happened, business impact,
  remediation), a hard quiz per vuln (perfect score required), and a leaderboard.
- **Per-player flags**: every flag is `FLAG{vuln_HMAC(your_key + pepper)}`, unique and unshareable.
- **Scoring**: 100 for the flag, 50 for passing the quiz. Leaderboard ranks by points, then speed.

## Make it fun, status
- Flag format `FLAG{...}`, per-player and unshareable. DONE.
- Scoreboard / leaderboard. DONE.
- Learn-then-quiz after each capture (business impact + remediation). DONE.
- Progressive stage unlock, Stage 2 briefing gated on full Stage 1 capture. DONE.
- Red herrings and lore breadcrumbs in the target. PARTIAL.
- Hardened "blue-team" branch to diff exploit vs fix. NEXT.
- Difficulty dial, hint economy (hints cost points). DONE.
