# Hardened branch, blue-team reference

This `hardened` branch is the remediated twin of the vulnerable target on `main`. Use it to
**learn the fix, not just the exploit**: read the diff, then run it and confirm each Stage 1
attack now fails.

## See the fixes
```bash
git diff main..hardened -- web/        # every exploit next to its fix
```

## Confirm the exploits are dead
```bash
git checkout hardened
docker compose up --build -d           # target only, http://127.0.0.1:8080
```
Then re-run your Stage 1 attacks and watch them fail.

## What changed, by vulnerability
| Vuln | Fix | File(s) |
|------|-----|---------|
| SQL injection | Parameterised queries, real prepares (no stacked queries) | login.php, track.php, support.php, api/v1 |
| Reflected + stored XSS | Output encoding everywhere (htmlspecialchars/nl2br) + CSP header | track.php, support.php, 000-default.conf |
| IDOR | Ownership checks (self or admin) on every object read | shipment.php, api/v1 |
| Privilege escalation / mass assignment | Allow-list of editable fields, role not user-writable | profile.php, api/v1 |
| Auth failures | Generic login/reset messages (no enumeration), session regen | login.php, reset.php |
| Weak password storage | password_verify + transparent upgrade from MD5 to bcrypt | login.php, reset.php |
| Insecure design (reset) | Random single-use time-limited token, rate limiting | reset.php |
| Security misconfiguration | display_errors off, expose_php off, no phpinfo, no dir listing, leaked comment removed | Dockerfile, 000-default.conf, admin.php, index.php |
| SSRF | Scheme allow-list, resolve host, block private/reserved ranges, no redirects | fetch.php, api/v1 |
| RCE via upload | Image validation, random name, uploads directory cannot execute PHP | upload.php, 000-default.conf, Dockerfile |
| OS command injection | Admin only, strict input validation, escapeshellarg | netcheck.php |
| API BOLA / BFLA | Object- and function-level authorization checks | api/v1/index.php |
| API excessive data exposure | Minimal response objects (no ssn/token/hash) | api/v1/index.php |
| API unrestricted consumption | Pagination with capped limit | api/v1/index.php |
| API wide-open CORS | Wildcard + credentials removed | api/v1/.htaccess |
| Improper inventory (legacy API) | v0 decommissioned and removed | api/v0 deleted |

## Teaching use
Run a session on `main`, capture the flags, then `git checkout hardened` and have players retry the
same attacks. What used to score now returns 403s, rejections and empty results. That contrast is
the lesson.

## Added fixes (A06, A07, A09 and API2/4/6/8)
This branch now covers all 20 challenges. The newer ones:

| Vuln | Fix | File(s) |
|------|-----|---------|
| Vulnerable component / SSTI | Template engine replaced with a fixed allow-list of tokens, no eval | report.php |
| Auth failures (brute force) | Rate limiting + lockout on login, strong password on the driver account | login.php, db/00-init.sql |
| Logging failures | Exposed debug log removed, robots.txt no longer advertises paths | logs.php (deleted), robots.txt |
| API broken auth | Token accepted from the header only, never the query string; /whoami leaks nothing | api/v1/index.php |
| API resource consumption | Page size capped (max 50) | api/v1/index.php |
| API business flow | /orders/bulk quantity capped (1-50) | api/v1/index.php |
| API misconfiguration | Debug /config endpoint removed; CORS same-origin | api/v1/index.php, api/v1/.htaccess |

> `hardened` is now rebuilt from the current `main`, so `git diff main..hardened` shows
> exactly the vulnerable-to-fixed change for all 20 challenges, nothing else.
