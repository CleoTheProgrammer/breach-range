<?php
require_once __DIR__ . '/db.php';
if (empty($_SESSION['uid'])) { header('Location: /login.php'); exit; }
// Fixed: verify the file really is an image, allow only jpg/png, store under a
// random name. The uploads directory no longer executes code (Apache + Dockerfile),
// so an uploaded script cannot run.
$out = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['photo']['tmp_name'])) {
    $info = @getimagesize($_FILES['photo']['tmp_name']);
    $allowed = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png'];
    if ($info && isset($allowed[$info[2]])) {
        $name = bin2hex(random_bytes(8)) . '.' . $allowed[$info[2]];
        move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/uploads/' . $name);
        $out = 'Uploaded.';
    } else {
        $out = 'Rejected: only JPG or PNG images are accepted.';
    }
}
require_once __DIR__ . '/includes/header.php'; ?>
<div class="card" style="max-width:480px">
  <h2 style="margin-top:0">Upload a profile photo</h2>
  <form method="post" enctype="multipart/form-data">
    <input type="file" name="photo">
    <button class="btn amber" type="submit">Upload</button>
  </form>
  <?php if ($out): ?><p><?= htmlspecialchars($out) ?></p><?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
