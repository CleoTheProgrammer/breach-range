<?php
// Content for every challenge: lesson (what / business impact / remediation) and a hard quiz.
// Keys MUST match the vuln ids the target uses in pf()/cflag().
function challenges() {
  return [
  'sqli' => [
    'title'=>'SQL Injection, authentication bypass','owasp'=>'Web A03:2021 Injection','surface'=>'web',
    'what'=>"The login builds its query by pasting your input straight into SQL, so a payload like ' OR '1'='1' -- changes the query logic and logs you in with no valid password.",
    'impact'=>'For Kestrel this is a full account takeover from an unauthenticated form. An attacker can log in as any customer or as the administrator, read the whole customer database, and pivot into billing. In regulated terms it is a reportable breach of personal data (POPIA in South Africa), with direct liability and loss of customer trust.',
    'fix'=>'Use parameterised queries / prepared statements for every query, never string concatenation. Apply least-privilege to the DB account, validate input types, and add a WAF rule as defence in depth, not as the primary control.',
    'quiz'=>[
      ['q'=>'Why do parameterised queries stop SQL injection where escaping often fails?','opts'=>['They encrypt the query in transit','The data is sent separately from the query plan, so input can never change the SQL structure','They make the query run faster','They hide error messages'],'a'=>1],
      ['q'=>'An ORM is used but one report runs a raw concatenated query for a LIKE search. What is true?','opts'=>['The ORM protects all queries automatically','The raw query is still injectable and must be parameterised','LIKE queries cannot be injected','Only INSERTs are at risk'],'a'=>1],
      ['q'=>'Least privilege on the app DB user primarily limits:','opts'=>['The speed of exploitation','The blast radius once an injection succeeds','The number of columns returned','Nothing, it is cosmetic'],'a'=>1],
    ],
  ],
  'idor' => [
    'title'=>'Insecure Direct Object Reference (IDOR)','owasp'=>'Web A01:2021 Broken Access Control','surface'=>'web',
    'what'=>"Shipment records are fetched by id with no check that the record belongs to you, so changing the id in the URL reveals other customers' shipments, including a restricted one.",
    'impact'=>"Any logged-in customer can enumerate every shipment Kestrel handles: routes, declared values, and internal notes. That leaks competitors' logistics, high-value cargo timing (a physical-theft risk), and personal data, all without any alarm firing.",
    'fix'=>'Enforce an ownership/authorisation check on every object access (the record must belong to, or be shared with, the requester). Prefer non-sequential identifiers, and centralise access checks so no endpoint can forget them.',
    'quiz'=>[
      ['q'=>'Replacing sequential ids with UUIDs is:','opts'=>['A complete fix for IDOR','Helpful obfuscation but not a substitute for an authorisation check','Slower and pointless','Only relevant to APIs'],'a'=>1],
      ['q'=>'The strongest fix for IDOR is:','opts'=>['Hiding the id in a POST body','A server-side check that the object belongs to the caller','Client-side validation','Rate limiting'],'a'=>1],
      ['q'=>'Why did no alert fire during enumeration here?','opts'=>['The attack was encrypted','There is no access logging or anomaly detection on object reads','IDOR is undetectable by design','The firewall blocked logging'],'a'=>1],
    ],
  ],
  'privesc' => [
    'title'=>'Privilege escalation via mass assignment','owasp'=>'Web A08:2021 / Broken Access Control','surface'=>'web',
    'what'=>'The profile update binds every field you submit, including role. Posting role=admin promotes your own customer account to administrator.',
    'impact'=>'A paying customer becomes a Kestrel administrator, gaining every user API token, the ability to alter shipments and invoices, and access to admin-only tooling. This is the classic customer-to-admin takeover that turns a minor account into total control of the platform.',
    'fix'=>'Never bind client input to privileged fields. Use an explicit allow-list of user-editable fields, keep authorisation attributes server-controlled, and require a separate, audited workflow for role changes.',
    'quiz'=>[
      ['q'=>'Mass assignment is dangerous because:','opts'=>['It is slow','Client-supplied fields are trusted and can set attributes the user should not control','It only affects JSON APIs','It exposes stack traces'],'a'=>1],
      ['q'=>'The correct pattern is:','opts'=>['Deny-list known-bad fields','Allow-list exactly the fields a user may edit','Trust the frontend form','Hash the role field'],'a'=>1],
      ['q'=>'Role changes should additionally be:','opts'=>['Instant and silent','Audited and separated from self-service profile edits','Done client-side','Stored in a cookie'],'a'=>1],
    ],
  ],
  'xss' => [
    'title'=>'Stored Cross-Site Scripting','owasp'=>'Web A03:2021 Injection','surface'=>'web',
    'what'=>'Support ticket text is stored and rendered without encoding, so a script you submit runs in the browser of any staff member or customer who views the ticket queue.',
    'impact'=>"An attacker scripts the session of a Kestrel support agent or admin who opens the ticket, stealing their session, performing actions as them, or seeding a worm across the queue. It weaponises the victim's trust and privileges without touching their password.",
    'fix'=>'Context-aware output encoding on every rendered value, a strict Content-Security-Policy, HttpOnly cookies, and input validation. Treat all stored data as untrusted at render time.',
    'quiz'=>[
      ['q'=>'Stored XSS is more severe than reflected XSS mainly because:','opts'=>['It is faster','The payload persists and fires for every viewer with no social engineering','It bypasses TLS','It only affects admins'],'a'=>1],
      ['q'=>'A strict CSP helps by:','opts'=>['Encrypting the DOM','Restricting which scripts can execute, reducing injected-script impact','Blocking SQL injection','Disabling cookies'],'a'=>1],
      ['q'=>'HttpOnly on the session cookie:','opts'=>['Stops XSS entirely','Prevents script from reading the cookie, limiting session theft','Encrypts the cookie','Is only for CSRF'],'a'=>1],
    ],
  ],
  'crypto' => [
    'title'=>'Cryptographic failure, weak password storage','owasp'=>'Web A02:2021 Cryptographic Failures','surface'=>'web',
    'what'=>'Passwords are stored as unsalted MD5. These hashes are trivially cracked with wordlists/rainbow tables, so the admin password falls quickly once the hashes leak.',
    'impact'=>"When Kestrel's user table leaks (via the SQLi or the legacy API here), every password is effectively plaintext. Because people reuse passwords, the damage spreads to staff email, banking and other services, multiplying a single breach into many.",
    'fix'=>'Store passwords with a slow, salted, memory-hard algorithm (bcrypt, scrypt or Argon2). Never use MD5/SHA1 for passwords. Add MFA, and encrypt sensitive fields at rest with managed keys.',
    'quiz'=>[
      ['q'=>'Why is unsalted MD5 unsuitable for passwords?','opts'=>['It is too long','It is fast and unsalted, so it is cheap to brute-force and precompute','It cannot store letters','It is not standardised'],'a'=>1],
      ['q'=>'bcrypt/Argon2 resist cracking chiefly because they are:','opts'=>['Encrypted','Deliberately slow and memory-hard, with per-password salts','Reversible','Shorter'],'a'=>1],
      ['q'=>'A salt prevents:','opts'=>['Network sniffing','Precomputed (rainbow table) attacks across identical passwords','SQL injection','XSS'],'a'=>1],
    ],
  ],
  'idesign' => [
    'title'=>'Insecure design, guessable reset code','owasp'=>'Web A04:2021 Insecure Design','surface'=>'web',
    'what'=>'The password-reset code is a 4-digit number derived from the user id, with no rate limiting or lockout, so it can be computed or brute-forced in moments.',
    'impact'=>'An attacker resets any Kestrel account at will, including the administrator, with no credentials and no detection. This is a design flaw: no amount of patching a line of code fixes it, the whole recovery flow was conceived insecurely.',
    'fix'=>'Design the flow securely: long, random, single-use, time-limited tokens delivered out of band, strict rate limiting and lockout, and abuse monitoring. Threat-model high-risk flows before building them.',
    'quiz'=>[
      ['q'=>'Insecure design differs from an implementation bug because:','opts'=>['It is always XSS','The flaw is in how the feature was conceived, not a single coding mistake','It only affects legacy code','It cannot be exploited'],'a'=>1],
      ['q'=>'A secure reset token should be:','opts'=>['Short and memorable','Long, random, single-use and time-limited','Derived from the user id','The same each time'],'a'=>1],
      ['q'=>'Rate limiting the reset endpoint primarily stops:','opts'=>['SQL injection','Brute-force guessing of the code','Weak hashing','CSRF'],'a'=>1],
    ],
  ],
  'misconfig' => [
    'title'=>'Security misconfiguration','owasp'=>'Web A05:2021 Security Misconfiguration','surface'=>'web',
    'what'=>'Verbose errors, directory listing, exposed phpinfo and default credentials hand an attacker a map of the system and its secrets.',
    'impact'=>'Misconfiguration accelerates every other attack: leaked paths, versions and the DB password shorten the time from recon to compromise for Kestrel, and often expose data with no exploit needed at all.',
    'fix'=>'Harden by default: disable debug output and directory listing in production, remove diagnostic endpoints, change all default credentials, and manage configuration as code with automated checks.',
    'quiz'=>[
      ['q'=>'display_errors=On in production is dangerous because:','opts'=>['It slows the app','It leaks queries, paths and internals that aid attackers','It breaks CSS','It disables logging'],'a'=>1],
      ['q'=>'The best way to prevent config drift is:','opts'=>['Manual checklists only','Configuration as code with automated hardening checks','Trusting defaults','Hiding the admin page'],'a'=>1],
      ['q'=>'An exposed phpinfo page mainly gives an attacker:','opts'=>['Nothing useful','Detailed environment, versions and paths for targeting','Admin access directly','A password'],'a'=>1],
    ],
  ],
  'ssrf' => [
    'title'=>'Server-Side Request Forgery','owasp'=>'Web A10:2021 SSRF','surface'=>'web',
    'what'=>'The manifest importer fetches any URL you supply from the server, so you can make the server request internal-only services it trusts.',
    'impact'=>"An attacker uses Kestrel's own server to reach internal services, cloud metadata endpoints and admin panels that are firewalled from the internet. In cloud environments SSRF frequently yields credentials and full account compromise.",
    'fix'=>'Enforce an allow-list of permitted destinations, block private/link-local ranges and redirects, resolve and pin DNS, and isolate the fetch service. Never send a raw user URL to an unrestricted HTTP client.',
    'quiz'=>[
      ['q'=>'SSRF is especially dangerous in cloud because:','opts'=>['It is faster there','It can reach the instance metadata service to steal credentials','Clouds have no firewalls','It only works on AWS'],'a'=>1],
      ['q'=>'The most robust SSRF control is:','opts'=>['Blocking the word localhost','An allow-list of destinations plus blocking private ranges and redirects','Rate limiting','Encoding the URL'],'a'=>1],
      ['q'=>'Why is a deny-list of IPs often insufficient?','opts'=>['It is too strict','DNS rebinding, redirects and alternate encodings bypass it','It blocks real users','It is deprecated'],'a'=>1],
    ],
  ],
  'rce' => [
    'title'=>'Remote Code Execution via file upload','owasp'=>'Web A01/A05, RCE','surface'=>'web',
    'what'=>'The upload accepts any file with no validation and stores it in a directory that executes PHP, so an uploaded script runs as server code. The command-injection admin tool is a second path to execution.',
    'impact'=>"This is the worst case: an attacker runs arbitrary commands on Kestrel's server, reads any file, pivots to the internal network and establishes persistence. It is a complete compromise of the host, not just the application.",
    'fix'=>'Validate type by content not extension, store uploads outside the web root on storage that cannot execute, randomise names, and never pass user input to a shell. Run the app as a low-privilege user and segment the network.',
    'quiz'=>[
      ['q'=>'Checking only the file extension is weak because:','opts'=>['Extensions are encrypted','Content and server handler mapping, not the extension, decide execution','It is slow','Extensions are case sensitive'],'a'=>1],
      ['q'=>'The safest place for uploads is:','opts'=>['The web root for speed','Outside the web root, on non-executable storage','In the database as PHP','In /tmp with exec on'],'a'=>1],
      ['q'=>'Command injection in the ping tool is best fixed by:','opts'=>['Escaping quotes only','Avoiding the shell, using an argument array / native API with strict input validation','Hiding the page','Rate limiting'],'a'=>1],
    ],
  ],
  'bola' => [
    'title'=>'API Broken Object Level Authorization (BOLA)','owasp'=>'API1:2023 BOLA','surface'=>'api',
    'what'=>'The API returns any user or shipment object by id without checking that the caller owns it, so one valid token reads everyone records.',
    'impact'=>"BOLA is the number one API risk. With a single partner token an attacker scrapes Kestrel's entire customer base, including the SSN and token fields the API over-returns, enabling fraud and identity theft at scale.",
    'fix'=>'Check object-level authorisation on every request against the authenticated identity. Do not rely on the id being unguessable. Centralise the check and test it per endpoint.',
    'quiz'=>[
      ['q'=>'BOLA is primarily an authorisation failure at the level of:','opts'=>['The network','Individual objects/records the caller should not access','The TLS handshake','The rate limiter'],'a'=>1],
      ['q'=>'Automated scanners miss many BOLA issues because:','opts'=>['They are encrypted','Valid-looking requests need business-context to know who should access what','APIs have no auth','BOLA is theoretical'],'a'=>1],
      ['q'=>'The fix is applied:','opts'=>['Once at the gateway only','Per request, per object, against the authenticated user','In the client app','By obscuring ids'],'a'=>1],
    ],
  ],
  'bfla' => [
    'title'=>'API Broken Function Level Authorization (BFLA)','owasp'=>'API5:2023 BFLA','surface'=>'api',
    'what'=>'The admin API endpoint accepts any valid token and never checks role, so a normal partner token invokes administrative functions.',
    'impact'=>'A low-privilege partner performs admin-only operations on Kestrel: dumping all users with tokens and SSNs, and potentially altering data. Function-level gaps turn a limited credential into administrative power.',
    'fix'=>'Enforce role/permission checks on every administrative function server-side, deny by default, and keep admin routes separate with explicit authorisation, not just authentication.',
    'quiz'=>[
      ['q'=>'BFLA differs from BOLA in that it concerns:','opts'=>['Objects vs networks','Access to functions/operations rather than individual records','Encryption strength','Rate limits'],'a'=>1],
      ['q'=>'Deny-by-default means:','opts'=>['Everything is allowed unless blocked','Access is refused unless explicitly granted','Only GET is allowed','Admins are blocked'],'a'=>1],
      ['q'=>'Authentication without authorization here means:','opts'=>['The token is invalid','A valid token is wrongly treated as sufficient for privileged actions','TLS is off','The user is anonymous'],'a'=>1],
    ],
  ],
  'massassign' => [
    'title'=>'API mass assignment','owasp'=>'API3:2023 Broken Object Property Level Authorization','surface'=>'api',
    'what'=>'The PATCH endpoint writes any JSON property you send to the user record, including role and other protected fields.',
    'impact'=>'An attacker escalates privileges or tampers with protected attributes directly through the API, bypassing the UI entirely. It also lets them over-read sensitive properties the API should never expose.',
    'fix'=>'Allow-list writable properties server-side, separate read and write schemas (DTOs), and never map request bodies straight onto database entities.',
    'quiz'=>[
      ['q'=>'Using DTOs / schemas for API writes helps because:','opts'=>['They are faster','Only explicitly listed properties are accepted and bound','They encrypt data','They cache responses'],'a'=>1],
      ['q'=>'Over-returning properties like ssn and token is:','opts'=>['Fine if documented','Excessive data exposure that aids attackers','Required by REST','A performance feature'],'a'=>1],
      ['q'=>'Binding request bodies directly to DB entities is risky because:','opts'=>['It is slow','Clients can set fields they should not control','It breaks JSON','It needs more memory'],'a'=>1],
    ],
  ],
  'invapi' => [
    'title'=>'Improper inventory management, forgotten API','owasp'=>'API9:2023 Improper Inventory Management','surface'=>'api',
    'what'=>'An old, unauthenticated v0 API was never decommissioned and still returns the user list to anyone.',
    'impact'=>"Shadow and legacy endpoints are a leading cause of breaches: Kestrel's security effort went to v1, while the forgotten v0 quietly exposes data with no auth at all. You cannot protect what you do not know is running.",
    'fix'=>'Maintain a live inventory of every API and version, retire old ones on a schedule, apply the same auth and monitoring everywhere, and use a gateway to discover and govern endpoints.',
    'quiz'=>[
      ['q'=>'Legacy/shadow APIs are dangerous mainly because:','opts'=>['They are slow','They often lack the controls applied to current versions and go unmonitored','They use more memory','They are always internal'],'a'=>1],
      ['q'=>'A core control for this risk is:','opts'=>['Blocking all APIs','A maintained inventory with a decommissioning process','Faster servers','Client-side auth'],'a'=>1],
      ['q'=>'Why does effort on v1 not protect v0?','opts'=>['It does','Controls are per endpoint/version; an untracked version inherits none','v0 is encrypted','v0 cannot be reached'],'a'=>1],
    ],
  ],
  ];
}
