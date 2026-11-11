<?php require_once __DIR__ . '/../db.php'; $P = current_player(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Breach Range Console, Stage 1</title>
  <style>
    :root { --bg:#0b1016; --panel:#121a24; --line:#223042; --ink:#e6eef6; --dim:#8aa0b4;
            --amber:#f4a400; --green:#3ad29f; --red:#ff6b6b; }
    * { box-sizing:border-box; }
    body { margin:0; font-family:'Segoe UI',system-ui,sans-serif; background:var(--bg); color:var(--ink); }
    a { color:var(--amber); text-decoration:none; }
    header.bar { background:#070b10; border-bottom:1px solid var(--line); padding:14px 22px; display:flex; align-items:center; gap:16px; }
    header.bar .brand { font-weight:700; letter-spacing:1px; }
    header.bar .brand span { color:var(--amber); }
    header.bar .tag { background:var(--amber); color:#070b10; font-weight:700; font-size:11px; padding:2px 8px; border-radius:3px; letter-spacing:1px; }
    header.bar nav { margin-left:auto; display:flex; gap:18px; font-size:14px; }
    header.bar nav a { color:var(--dim); } header.bar nav a:hover { color:var(--ink); }
    .wrap { max-width:1000px; margin:28px auto; padding:0 20px; }
    .panel { background:var(--panel); border:1px solid var(--line); border-radius:10px; padding:22px; margin-bottom:20px; }
    .btn { background:var(--amber); color:#070b10; border:none; padding:11px 22px; border-radius:6px; cursor:pointer; font-size:15px; font-weight:700; }
    .btn.ghost { background:transparent; color:var(--amber); border:1px solid var(--amber); }
    input,select { width:100%; padding:10px; border:1px solid var(--line); border-radius:6px; margin:6px 0 14px; background:#0c131b; color:var(--ink); font-size:14px; }
    label { font-size:13px; color:var(--dim); font-weight:600; }
    table { width:100%; border-collapse:collapse; }
    th,td { text-align:left; padding:10px 12px; border-bottom:1px solid var(--line); font-size:14px; }
    th { color:var(--dim); font-weight:600; }
    .pill { font-size:11px; padding:2px 9px; border-radius:20px; font-weight:700; }
    .pill.todo { background:#2a2130; color:var(--dim); }
    .pill.flag { background:rgba(244,164,0,.15); color:var(--amber); }
    .pill.done { background:rgba(58,210,159,.15); color:var(--green); }
    code { background:#0c131b; padding:2px 6px; border-radius:4px; color:var(--green); }
    .flash { padding:10px 14px; border-radius:6px; margin-bottom:16px; font-size:14px; }
    .flash.ok { background:rgba(58,210,159,.12); color:var(--green); border:1px solid rgba(58,210,159,.3); }
    .flash.err { background:rgba(255,107,107,.12); color:var(--red); border:1px solid rgba(255,107,107,.3); }
  </style>
</head>
<body>
<header class="bar">
  <div class="brand">BREACH<span>RANGE</span> CONSOLE</div>
  <span class="tag">Stage 1</span>
  <nav>
    <a href="/index.php">Briefing</a>
    <?php if ($P): ?>
      <a href="/challenges.php">Challenges</a>
      <a href="/leaderboard.php">Leaderboard</a>
      <a href="/logout.php"><?= htmlspecialchars($P['handle']) ?> (exit)</a>
    <?php else: ?>
      <a href="/leaderboard.php">Leaderboard</a>
    <?php endif; ?>
  </nav>
</header>
<div class="wrap">
<?php if (!empty($_SESSION['flash'])): $f=$_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="flash <?= $f[0] ?>"><?= htmlspecialchars($f[1]) ?></div>
<?php endif; ?>
