<?php
// Stage 2, "Inside the Fence": internal-network challenges reached by pivoting from
// the Stage 1 foothold. Keys must match the pf()/cflag() ids the internal services use.

function challenges_s2() {
  return [
  's2_intranet' => [
    'title'=>'Unauthenticated internal intranet','owasp'=>'Stage 2, Internal network trust','surface'=>'internal','stage'=>2,
    'what'=>"The staff intranet is reachable only from inside the network and assumes anything on the LAN is trusted, so its admin dashboard has no authentication. From your foothold on the perimeter host you reach it directly and read it.",
    'impact'=>"A flat, trusted internal network means one foothold exposes everything. An attacker who lands on any internal host reads the employee directory and internal systems with nothing asking who they are, the classic reason a single breach becomes total.",
    'fix'=>"Authenticate and authorise every service, internal ones included (zero trust). Segment the network and never treat 'internal' as a security boundary on its own.",
    'quiz'=>[
      ['q'=>'Why is an unauthenticated internal service dangerous once an attacker has any foothold?','opts'=>['It is slower','The attacker is now on the trusted network and the service asks nothing','Internal services are encrypted','It only exposes public data'],'a'=>1],
      ['q'=>'Zero trust means:','opts'=>['Trust the LAN, verify the internet','Verify every request regardless of network location','No passwords anywhere','Block all internal traffic'],'a'=>1],
      ['q'=>'Network segmentation primarily limits:','opts'=>['Bandwidth','How far an attacker can move after one foothold','Password strength','Log volume'],'a'=>1],
    ],
  ],
  's2_credreuse' => [
    'title'=>'Credential reuse into the finance service','owasp'=>'Stage 2, Credential Reuse','surface'=>'internal','stage'=>2,
    'what'=>"The internal finance service accepts the very password that leaked on the perimeter portal (the shared database password). Reusing that looted credential logs you straight into the finance data.",
    'impact'=>"Reused passwords turn one leak into many compromises. A single perimeter secret opens the finance service, its invoices and banking details, a direct path to fraud and a reportable data breach.",
    'fix'=>"Use unique credentials per system, a secrets manager and MFA. Never share one password across services, and rotate anything that leaks immediately.",
    'quiz'=>[
      ['q'=>'Credential reuse is dangerous because:','opts'=>['It is slow to type','One leaked password unlocks many unrelated systems','Passwords expire','It uses more memory'],'a'=>1],
      ['q'=>'The best defence against reuse across systems is:','opts'=>['Longer passwords only','Unique per-system secrets in a vault, plus MFA','Changing passwords yearly','Sharing one strong password'],'a'=>1],
      ['q'=>'After a password is found leaked, you should:','opts'=>['Keep using it','Rotate it everywhere it was reused','Email it to the team','Hash it again'],'a'=>1],
    ],
  ],
  's2_secrets' => [
    'title'=>'Secrets exposed by an internal service','owasp'=>'Stage 2, Secrets Exposure','surface'=>'internal','stage'=>2,
    'what'=>"An internal deploy/config service exposes its environment on a debug endpoint, leaking cloud credentials in plaintext to anyone who can reach it from inside.",
    'impact'=>"Leaked cloud keys are the pivot from the corporate network into the cloud account, often the real crown jewels. Secrets sprawl in internal tooling is a leading cause of full-environment compromise.",
    'fix'=>"Keep secrets in a managed vault, never in environment dumps or debug endpoints; disable debug in production; and scope and rotate credentials so a leak is limited and short-lived.",
    'quiz'=>[
      ['q'=>'Why are leaked cloud keys especially serious from inside a corporate network?','opts'=>['They are long','They pivot the attacker into the cloud account and its data','They slow the server','They are already public'],'a'=>1],
      ['q'=>'A debug endpoint that dumps environment variables is:','opts'=>['A useful feature to keep on','A secrets-exposure risk that must be off in production','Required by cloud providers','Harmless if internal'],'a'=>1],
      ['q'=>'Secrets belong in:','opts'=>['Environment dumps','A managed secrets vault with scoped, rotatable access','Source code comments','The database as plaintext'],'a'=>1],
    ],
  ],
  ];
}

function hints_s2() {
  return [
    's2_intranet'  => "From your shell on the portal host, reach the internal intranet by name: curl -H 'X-Player-Key: YOURKEY' http://intranet/",
    's2_credreuse' => "The finance service (http://finance/) wants a password. You already looted one on the perimeter, the shared DB password. Reuse it: ?pw=...",
    's2_secrets'   => "The deploy/config service (http://secrets/) has a debug switch. Try http://secrets/?debug=1 with your X-Player-Key header.",
  ];
}
