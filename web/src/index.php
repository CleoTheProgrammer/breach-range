<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="card">
  <h1 style="margin-top:0">Welcome to the Kestrel Freight customer portal</h1>
  <p>Track shipments, download invoices and manage your account. Our new
     self-service portal is now live for all customers and B2B partners.</p>
  <p><a class="btn amber" href="/track.php">Track a shipment</a>
     <a class="btn" href="/login.php">Sign in to your account</a></p>
</div>

<div class="card">
  <h2 style="margin-top:0">This is your target</h2>
  <p>Kestrel rushed this portal and its partner API at <code>/api/v1/</code> to launch. Corners
     were cut. Your job is to find them. Each weakness you exploit reveals a <code>FLAG{...}</code>
     that is unique to you, submit it back in the Breach Range console to score and learn why it matters.</p>
  <p style="color:#8296a8;font-size:13px">No key set yet? Open the console, enlist, and hit
     <b>Launch target</b> so your personalised flags appear.</p>
</div>

<div class="card">
  <h3 style="margin-top:0">Status board</h3>
  <table>
    <tr><th>Service</th><th>State</th><th>Notes</th></tr>
    <tr><td>Customer portal</td><td>Online</td><td>v0.9.2 (beta)</td></tr>
    <tr><td>Partner API /api/v1</td><td>Online</td><td>token auth</td></tr>
    <tr><td>Invoice service</td><td>Degraded</td><td>maintenance</td></tr>
  </table>
  <!-- TODO before GA: rotate the shared DB password (Summer2023!) and turn off display_errors -->
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
