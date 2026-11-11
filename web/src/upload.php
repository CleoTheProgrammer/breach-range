<?php
require_once __DIR__ . '/db.php';
if (empty($_SESSION['uid'])) { header('Location: /login.php'); exit; }
// A01/RCE: profile photo upload with no type, extension or content validation,
// written into a directory that executes PHP. Drop a .php webshell -> code execution.
$out = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['photo']['name'])) {
    $name = basename($_FILES['photo']['name']);          // attacker-controlled name/ext
    $dest = __DIR__ . '/uploads/' . $name;
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
        $out = 'Uploaded to /uploads/' . htmlspecialchars($name);
    } else { $out = 'Upload failed.'; }
}
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card" style="max-width:480px">
  <h2 style="margin-top:0">Upload a profile photo</h2>
  <form method="post" enctype="multipart/form-data">
    <input type="file" name="photo">
    <button class="btn amber" type="submit">Upload</button>
  </form>
  <?php if ($out): ?><p><?= $out ?></p><?php endif; ?>
  <p style="font-size:12px;color:#8296a8">Accepted: JPG or PNG, up to 20&nbsp;MB.</p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
