<?php
// Additional Stage 1 challenges (A06, A07, A09 and API2/4/6/8) and their hints,
// merged into the main sets by content.php (challenges) and hint.php (hints).

function challenges_more() {
  return [
  'components' => [
    'title'=>'Vulnerable and outdated component (SSTI)','owasp'=>'Web A06:2021 Vulnerable and Outdated Components','surface'=>'web',
    'what'=>"The report builder runs on SimpleTpl v1.0, an unmaintained template helper that evaluates {{ expr }} with eval(). That known flaw becomes server-side template injection and code execution.",
    'impact'=>"Kestrel shipped a dependency nobody updated. One outdated library hands an attacker code execution on the server, the same end state as the upload RCE, reached entirely through a component the team never wrote or reviewed.",
    'fix'=>"Track every dependency and its version, patch on a schedule, remove what you do not use, and watch advisories. Run software composition analysis in CI and choose components that are actively maintained.",
    'quiz'=>[
      ['q'=>'Why are outdated components a top risk even when your own code is clean?','opts'=>['They are slower','Known, published vulnerabilities in dependencies are exploitable without finding anything new','They use more disk','They break the build'],'a'=>1],
      ['q'=>'Software composition analysis (SCA) does what?','opts'=>['Encrypts dependencies','Inventories dependencies and flags known-vulnerable versions','Minifies code','Runs the tests'],'a'=>1],
      ['q'=>'An unmaintained template engine that evaluates expressions leads to:','opts'=>['Faster rendering','Server-side template injection and code execution','Better caching','Nothing, templates are safe'],'a'=>1],
    ],
  ],
  'authn' => [
    'title'=>'Identification and authentication failures','owasp'=>'Web A07:2021 Identification and Authentication Failures','surface'=>'web',
    'what'=>"The driver account uses a top-of-the-wordlist password and the login has no lockout, throttling or MFA, so it falls to basic password guessing.",
    'impact'=>"Weak passwords plus no lockout mean any account is one short brute-force away. For Kestrel that is unauthorised access to customer and operational data, and a foothold for everything that follows.",
    'fix'=>"Enforce strong password rules, rate limiting and lockout, check credentials against breach lists, and require multi-factor authentication, especially for staff and admin accounts.",
    'quiz'=>[
      ['q'=>'Account lockout and rate limiting primarily defend against:','opts'=>['SQL injection','Automated password guessing and credential stuffing','XSS','SSRF'],'a'=>1],
      ['q'=>'Checking new passwords against breach lists helps because:','opts'=>['It is faster','Already-leaked passwords are the first ones attackers try','It shortens them','It encrypts them'],'a'=>1],
      ['q'=>'The single most effective control against stolen or guessed passwords is:','opts'=>['A longer username','Multi-factor authentication','A CAPTCHA on signup','A password hint'],'a'=>1],
    ],
  ],
  'logging' => [
    'title'=>'Security logging and monitoring failures','owasp'=>'Web A09:2021 Security Logging and Monitoring Failures','surface'=>'web',
    'what'=>"A debug log viewer was left exposed and even listed in robots.txt, and the app writes secrets into logs in cleartext. Attackers read the logs while real attacks go unrecorded.",
    'impact'=>"Two failures at once: sensitive values written to logs an attacker can read, and no detection of the attacks themselves. Breaches run undetected for months when nobody is watching, raising both cost and regulatory exposure.",
    'fix'=>"Log security events centrally, never log secrets, protect and monitor the logs, alert on anomalies, and rehearse incident response. Robots.txt is a crawler hint, never an access control.",
    'quiz'=>[
      ['q'=>'Why is listing a path in robots.txt a poor way to hide it?','opts'=>['It is case sensitive','Attackers read robots.txt specifically to find hidden paths','It blocks search engines only at night','It encrypts the path'],'a'=>1],
      ['q'=>'Writing API keys and tokens into application logs is dangerous because:','opts'=>['Logs are slow','Anyone who can read the logs gains those secrets','It fills the disk','It is against style guides'],'a'=>1],
      ['q'=>'The monitoring half of this risk means:','opts'=>['Watching CPU graphs','Detecting and alerting on attacks so they are not missed','Logging user mouse moves','Nothing actionable'],'a'=>1],
    ],
  ],
  'apiauth' => [
    'title'=>'API broken authentication','owasp'=>'API2:2023 Broken Authentication','surface'=>'api',
    'what'=>"The API accepts the token in the query string and the token never expires, so it leaks through browser history, proxy and server logs and referer headers, and stays valid forever.",
    'impact'=>"A token in a URL is copied, cached and logged everywhere. Once leaked it grants permanent access because it never expires, turning a minor log exposure into lasting account compromise.",
    'fix'=>"Carry tokens in headers only, keep them short-lived with refresh and rotation, bind them to the client, and revoke on logout. Never put secrets in URLs.",
    'quiz'=>[
      ['q'=>'Why is a token in the query string worse than in a header?','opts'=>['Headers are encrypted','URLs are logged, cached and shared in history, proxies and referers','Query strings are slower','It is not worse'],'a'=>1],
      ['q'=>'Tokens that never expire are dangerous because:','opts'=>['They are long','A single leak grants permanent access with no natural cut-off','They cost money','They cannot be revoked at all'],'a'=>1],
      ['q'=>'A good mitigation is:','opts'=>['Longer tokens only','Short-lived tokens with rotation and revocation','Base64 encoding','Putting them in two places'],'a'=>1],
    ],
  ],
  'apiresource' => [
    'title'=>'API unrestricted resource consumption','owasp'=>'API4:2023 Unrestricted Resource Consumption','surface'=>'api',
    'what'=>"The listing endpoint honours any page size with no cap, so a single request can demand unbounded data and work from the server.",
    'impact'=>"No limits let an attacker exhaust CPU, memory, bandwidth and database with cheap requests, degrading or downing Kestrel's service and inflating cloud bills. It is also the engine behind mass data scraping.",
    'fix'=>"Enforce pagination with hard maximums, rate and payload limits, timeouts and quotas, and monitor for abusive patterns.",
    'quiz'=>[
      ['q'=>'An uncapped page-size parameter lets an attacker:','opts'=>['Change the UI theme','Force the server to do unbounded work from one request','Bypass TLS','Read other headers'],'a'=>1],
      ['q'=>'The core control for this risk is:','opts'=>['Bigger servers','Hard limits: pagination caps, rate limits, quotas and timeouts','A faster database','Client-side throttling'],'a'=>1],
      ['q'=>'Beyond denial of service, no limits also enable:','opts'=>['Better caching','Mass scraping of data at scale','Stronger auth','Smaller responses'],'a'=>1],
    ],
  ],
  'apiflow' => [
    'title'=>'Unrestricted access to a sensitive business flow','owasp'=>'API6:2023 Unrestricted Access to Sensitive Business Flows','surface'=>'api',
    'what'=>"The bulk order flow places unlimited orders with no quantity cap and no anti-automation, so it can be driven at scale by a script.",
    'impact'=>"Business logic abused at machine speed: inventory exhaustion, fraud, scalping, or flooding operations with fake orders. Each request is valid, the damage is in doing it ten thousand times.",
    'fix'=>"Identify which flows are sensitive and add anti-automation: quantity caps, rate limits, CAPTCHA or device checks, and business-rule validation, not just technical authentication.",
    'quiz'=>[
      ['q'=>'This risk is about:','opts'=>['Broken encryption','A valid business flow that can be automated without limits','Missing TLS','A SQL bug'],'a'=>1],
      ['q'=>'The right defence is:','opts'=>['Only authentication','Anti-automation and business limits (caps, rate limits, device checks)','A longer token','Hiding the endpoint'],'a'=>1],
      ['q'=>'Why does normal auth not stop this?','opts'=>['It does','Each request is authorised; the abuse is the volume and intent','Auth is disabled','The user is anonymous'],'a'=>1],
    ],
  ],
  'apimisconfig' => [
    'title'=>'API security misconfiguration','owasp'=>'API8:2023 Security Misconfiguration','surface'=>'api',
    'what'=>"A debug endpoint was left enabled in production and returns environment and config details, and CORS is set to a wildcard. Both hand attackers information and access they should not have.",
    'impact'=>"Leaked config shortens the path to compromise, and permissive CORS lets malicious sites read authenticated API responses from victims' browsers. Misconfiguration is among the most common and most preventable API breaches.",
    'fix'=>"Harden by default: disable debug in production, restrict CORS to known origins without credentials, remove unused endpoints, and manage config as code with automated checks.",
    'quiz'=>[
      ['q'=>'A wildcard CORS policy with credentials allows:','opts'=>['Faster responses','Any site to read authenticated API data from a victim browser','Better caching','Stronger auth'],'a'=>1],
      ['q'=>'A debug endpoint in production is dangerous because:','opts'=>['It is slow','It leaks environment and config details that aid attackers','It uses memory','It disables logging'],'a'=>1],
      ['q'=>'The best way to avoid misconfiguration is:','opts'=>['Manual review only','Hardened defaults and configuration managed as code with checks','Trusting the framework','Hiding endpoints'],'a'=>1],
    ],
  ],
  ];
}

function hints_more() {
  return [
    'components'    => "The report builder runs on SimpleTpl v1.0. What does it do with {{ 7*7 }}?",
    'authn'        => "There is a driver account. Its password sits near the top of every wordlist, and the login never locks out.",
    'logging'      => "Attackers read robots.txt first. What path does it tell you not to visit?",
    'apiauth'      => "The API lets you pass your token in the URL. Try /api/v1/whoami?token=YOURTOKEN.",
    'apiresource'  => "The shipments endpoint takes a limit parameter. What if you ask for a million?",
    'apiflow'      => "There is a bulk order endpoint. Is anything stopping you from ordering 10000 at once?",
    'apimisconfig' => "The API index lists its endpoints. One is a debug/config endpoint that should never be in production.",
  ];
}
