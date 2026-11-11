<?php
require_once __DIR__ . '/db.php';
$P = current_player();
require_once __DIR__ . '/includes/header.php'; ?>

<div class="panel">
  <div style="color:#8aa0b4;font-size:12px;letter-spacing:2px;text-transform:uppercase">Operation brief, Stage 1</div>
  <h1 style="margin:6px 0 14px">Last Mile</h1>
  <p style="line-height:1.6">Kestrel Freight Solutions is a regional logistics firm. When a competitor went
     digital, Kestrel's board gave their small team eight weeks to ship a customer portal and a partner
     API. They made the deadline. They did not make it safe.</p>
  <p style="line-height:1.6">You are contracted to assess the perimeter before an acquisition. Everything an
     anonymous attacker can reach is in scope: the customer portal and the partner API. Get in without
     credentials, pivot from customer to administrator, reach what was never meant to face the internet,
     and find what the team forgot to switch off.</p>
  <p style="line-height:1.6">Every vulnerability carries a flag that is <b>unique to you</b>. Capture it on the
     target, submit it here, then learn why it matters and prove it on a short quiz. Points land on the
     leaderboard.</p>

  <?php if ($P): ?>
    <p style="margin-top:22px"><a class="btn" href="/challenges.php">Go, enter the range &rarr;</a></p>
  <?php else: ?>
    <div style="margin-top:22px;max-width:380px">
      <div style="color:#8aa0b4;font-size:12px;letter-spacing:2px;text-transform:uppercase;margin-bottom:8px">Enlist to begin</div>
      <form method="post" action="/auth.php">
        <label>Operator handle</label><input name="handle" maxlength="40" required>
        <label>Passphrase</label><input name="password" type="password" required>
        <button class="btn" type="submit">Go</button>
      </form>
      <p style="font-size:12px;color:#8aa0b4">New handle registers you. An existing handle logs you back in.</p>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
