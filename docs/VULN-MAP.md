# Stage 1, Vulnerability Map (ANSWER KEY, keep from players)

Target: Kestrel Freight, http://127.0.0.1:8080. Console: http://127.0.0.1:8081.
Flags are per-player: `FLAG{<vuln>_<12 hex = HMAC_SHA256(vuln|player_key, PEPPER)>}`.
The console recomputes and validates them. Challenge ids below match `pf()`/`cflag()`.

## Capture conditions (how each flag is revealed on the target)
| id | OWASP | Where / how to capture |
|----|-------|------------------------|
| sqli | Web A03 Injection | Login auth-bypass `' OR '1'='1' -- `; flag shown on dashboard (bypass detected) |
| idor | Web A01 Broken Access Control | `shipment.php?id=42`; flag in the restricted shipment's notes |
| privesc | Web A08 / BAC | POST `role=admin` to `profile.php`, then load `admin.php`; flag in the console |
| xss | Web A03 Injection | Store a `<script>`/`onerror=` payload in `support.php`; flag returned on submit |
| crypto | Web A02 Cryptographic Failures | Crack MD5 of `admin` (= "admin12345"), log in as admin, flag on `profile.php` |
| idesign | Web A04 Insecure Design | Reset a password via the guessable 4-digit code in `reset.php`; flag on success |
| misconfig | Web A05 Misconfiguration | As admin, open `admin.php?diag=1` (phpinfo); flag shown above it |
| ssrf | Web A10 SSRF | `fetch.php?url=http://127.0.0.1/internal.php`; flag in the internal response |
| rce | Web RCE (upload / cmd injection) | Upload a webshell OR inject in `netcheck.php`; `cat /var/flags/<player_key>.flag` |
| bola | API1 BOLA | `GET /api/v1/users/{id}` for an id that is not yours; `_flag` field appears |
| bfla | API5 BFLA | `GET /api/v1/admin/users` with any valid token; `flag` field |
| massassign | API3 Property-level auth | `PATCH /api/v1/users/{id}` with a JSON body; `flag` field |
| invapi | API9 Improper Inventory | `GET /api/v0/` (no auth); `flag` field |

API calls carry the player key via `?pk=<key>` or header `X-Player-Key`.

## Coverage note
- **Web Top 10**: A01 idor+privesc, A02 crypto, A03 sqli+xss, A04 idesign, A05 misconfig,
  A06 (outdated components, `v0.9.2` banner, lesson-level), A07 (auth failures via login enumeration
  + reset, folded into sqli/idesign), A08 privesc/massassign, A09 (logging failures, by absence),
  A10 ssrf. RCE upload sits under A01/A05.
- **API Top 10**: API1 bola, API2 (broken auth, token in query/no expiry, in the auth lesson),
  API3 massassign, API4 (no pagination on `/shipments?q=`), API5 bfla, API6 (business flows via
  repeated PATCH), API7/API10 (SSRF/unsafe consumption via `/import`), API8 (CORS `*` + verbose),
  API9 invapi.

## Seed logins
tshepo:123456789, lerato:letmein, admin:admin12345

## Added challenges (now 20 flags, one per OWASP item)
| id | OWASP | Where / how to capture |
|----|-------|------------------------|
| components | Web A06 Vulnerable/Outdated Components | `report.php?tpl={{ 7*7 }}` (SimpleTpl v1.0 SSTI via eval); flag on render |
| authn | Web A07 Auth Failures | Brute-force the `driver` account (password = `password`, no lockout); flag on dashboard |
| logging | Web A09 Logging Failures | `robots.txt` leaks `/logs.php`, an exposed debug log that over-logs secrets; flag in the log |
| apiauth | API2 Broken Authentication | `GET /api/v1/whoami?token=<token>` (token in URL); `flag` field |
| apiresource | API4 Unrestricted Resource Consumption | `GET /api/v1/shipments?limit=1000000` (no cap); `flag` field |
| apiflow | API6 Unrestricted Business Flow | `GET /api/v1/orders/bulk?count=10000` (no qty cap); `flag` field |
| apimisconfig | API8 Security Misconfiguration | `GET /api/v1/config` (debug endpoint left on); `flag` field |

New seed account: `driver` / `password` (weak, for the A07 brute-force challenge).
Note: these 7 live on `main`; the `hardened` branch currently remediates the original 13.
