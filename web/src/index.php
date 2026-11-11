<?php require_once __DIR__ . '/includes/header.php'; ?>
<div class="card">
  <h1 style="margin-top:0">Welcome to the Kestrel Freight customer portal</h1>
  <p>Track shipments, download invoices and manage your account.</p>
  <p><a class="btn amber" href="/track.php">Track a shipment</a>
     <a class="btn" href="/login.php">Sign in to your account</a></p>
</div>
<div class="card">
  <h3 style="margin-top:0">Status board</h3>
  <table>
    <tr><th>Service</th><th>State</th><th>Notes</th></tr>
    <tr><td>Customer portal</td><td>Online</td><td>v1.0.0</td></tr>
    <tr><td>Partner API /api/v1</td><td>Online</td><td>token auth</td></tr>
    <tr><td>Invoice service</td><td>Online</td><td>healthy</td></tr>
  </table>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
