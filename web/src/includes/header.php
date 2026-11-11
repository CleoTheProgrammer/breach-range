<?php require_once __DIR__ . '/../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kestrel Freight Solutions, Customer Portal</title>
  <style>
    :root { --navy:#122a3d; --amber:#f4a400; --bg:#f3f5f7; --line:#dce3ea; }
    * { box-sizing:border-box; }
    body { margin:0; font-family:'Segoe UI',system-ui,sans-serif; background:var(--bg); color:#1b2733; }
    header.topbar { background:var(--navy); color:#fff; padding:14px 22px; display:flex; align-items:center; gap:14px; }
    header.topbar .logo { font-weight:700; font-size:19px; letter-spacing:.3px; }
    header.topbar .logo span { color:var(--amber); }
    header.topbar nav { margin-left:auto; display:flex; gap:18px; }
    header.topbar nav a { color:#cfdae4; text-decoration:none; font-size:14px; }
    header.topbar nav a:hover { color:#fff; }
    .stage-flag { background:var(--amber); color:var(--navy); font-weight:700; font-size:12px;
                  padding:3px 10px; border-radius:3px; text-transform:uppercase; letter-spacing:1px; }
    .wrap { max-width:960px; margin:28px auto; padding:0 20px; }
    .card { background:#fff; border:1px solid var(--line); border-radius:8px; padding:22px; margin-bottom:20px; }
    .btn { background:var(--navy); color:#fff; border:none; padding:10px 18px; border-radius:5px; cursor:pointer; font-size:14px; }
    .btn.amber { background:var(--amber); color:var(--navy); }
    input,textarea { width:100%; padding:9px; border:1px solid var(--line); border-radius:5px; margin:6px 0 14px; font-size:14px; }
    label { font-size:13px; font-weight:600; color:#44566b; }
    table { width:100%; border-collapse:collapse; }
    th,td { text-align:left; padding:9px 10px; border-bottom:1px solid var(--line); font-size:14px; }
    th { background:#eef2f6; }
    footer { text-align:center; color:#8296a8; font-size:12px; padding:30px; }
  </style>
</head>
<body>
<header class="topbar">
  <div class="logo">KESTREL<span>FREIGHT</span></div>
  <span class="stage-flag">Stage 1</span>
  <nav>
    <a href="/index.php">Home</a>
    <a href="/track.php">Track</a>
    <a href="/dashboard.php">My Shipments</a>
    <a href="/support.php">Support</a>
    <?php if (!empty($_SESSION['uid'])): ?>
      <a href="/profile.php">Profile</a>
      <a href="/logout.php">Log out</a>
    <?php else: ?>
      <a href="/login.php">Sign in</a>
    <?php endif; ?>
  </nav>
</header>
<div class="wrap">
