<?php
// Hints (difficulty dial) and the Stage 2 gated briefing. Kept separate from
// content.php so the big challenge array stays readable.

define('HINT_COST', 20);

function hints() {
  return [
    'sqli'       => "The login pastes your input straight into SQL. What does a username of  admin' --  do to the query?",
    'idor'       => "Shipment ids are small sequential numbers. What happens when you open one that is not yours?",
    'privesc'    => "The profile form only submits name, email and phone. What if the POST body also carried role=admin?",
    'xss'        => "Support ticket text is shown to staff exactly as typed. Make it run, not just display.",
    'crypto'     => "The password column is a 32-char hex string: a fast, unsalted hash. Crack the admin one and log in.",
    'idesign'    => "The reset code is four digits and derived from the account. How many guesses would it really take?",
    'misconfig'  => "Once you are admin, the diagnostics link leaks far more than it should.",
    'ssrf'       => "The manifest importer fetches any URL from the server's own vantage point. Aim it inward.",
    'rce'        => "The photo upload never checks the file, and the uploads folder executes code.",
    'bola'       => "Your API token works. Now request a user or shipment id that is not yours.",
    'bfla'       => "There is an admin API endpoint. Does it actually verify you are an admin, or just that you have a token?",
    'massassign' => "PATCH accepts any JSON field. Which field would you never normally be allowed to set?",
    'invapi'     => "v1 is the new API. Was there a v0, is it still running, and does it even ask for a token?",
  ];
}

// Stage 1 is "cleared" when every flag is captured. Full marks also needs every quiz.
function stage2_brief() {
  return [
    'title' => 'Stage 2, Inside the Fence',
    'hook'  => "You own the perimeter: an administrator account and a shell on the portal host. "
             . "That host is not the prize, it is the doorway. Behind it sits Kestrel's corporate "
             . "network: a jump box the ops team RDPs into, an Active Directory domain, and the "
             . "'degraded' invoice service that was never really down, just moved somewhere you "
             . "could not reach. From the perimeter, from the credentials in that admin console.",
    'objectives' => [
      'Pivot from the portal host into the internal subnet',
      'Harvest and reuse the credentials and tokens you already looted',
      'Find the invoice service and the finance data it guards',
      'Reach the domain controller',
    ],
    'note' => "Stage 2 is the next build. Your Stage 1 completion key below is your proof of clearance "
            . "and will seed the Stage 2 environment.",
  ];
}
