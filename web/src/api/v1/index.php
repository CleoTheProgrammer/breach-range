<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../flags.php';
header('Content-Type: application/json');

// ---- auth (API2 Broken Authentication) --------------------------------------
function current_user() {
    $tok = $_SERVER['HTTP_X_API_TOKEN'] ?? ($_GET['token'] ?? '');
    if ($tok === '') return null;
    $s = db()->prepare("SELECT * FROM users WHERE api_token = ?");
    $s->execute([$tok]);
    return $s->fetch(PDO::FETCH_ASSOC) ?: null;
}
function out($data, $code = 200) { http_response_code($code); echo json_encode($data, JSON_PRETTY_PRINT); exit; }

$uri   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path  = trim(preg_replace('#^.*/api/v1#', '', $uri), '/');
$parts = $path === '' ? [] : explode('/', $path);
$me    = current_user();

if (!$parts) {
    out(['service' => 'Kestrel Partner API', 'version' => 'v1',
         'note' => 'send your player key as ?pk= or header X-Player-Key so flags are personalised',
         'endpoints' => ['/users/{id}', '/shipments/{id}', '/shipments?q=', '/admin/users', '/import?url=']]);
}

// GET /users/{id} -> API1 BOLA + API3 excessive data exposure
if ($parts[0] === 'users' && isset($parts[1]) && ctype_digit($parts[1]) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!$me) out(['error' => 'auth required'], 401);
    $s = db()->prepare("SELECT * FROM users WHERE id = ?");
    $s->execute([(int)$parts[1]]);
    $u = $s->fetch(PDO::FETCH_ASSOC);
    if (!$u) out(['error' => 'not found'], 404);
    if ((int)$parts[1] !== (int)$me['id']) { $u['_flag'] = pf('bola'); } // read someone else's object
    out($u);
}

// PATCH /users/{id} -> API3 mass assignment
if ($parts[0] === 'users' && isset($parts[1]) && $_SERVER['REQUEST_METHOD'] === 'PATCH') {
    if (!$me) out(['error' => 'auth required'], 401);
    $in = json_decode(file_get_contents('php://input'), true) ?: [];
    $sets = []; $vals = [];
    foreach ($in as $k => $v) { $sets[] = "`$k` = ?"; $vals[] = $v; }
    $vals[] = (int)$parts[1];
    db()->prepare("UPDATE users SET ".implode(',', $sets)." WHERE id = ?")->execute($vals);
    out(['updated' => true, 'flag' => pf('massassign')]);
}

// GET /shipments/{id} -> API1 BOLA
if ($parts[0] === 'shipments' && isset($parts[1]) && ctype_digit($parts[1])) {
    if (!$me) out(['error' => 'auth required'], 401);
    $s = db()->prepare("SELECT * FROM shipments WHERE id = ?");
    $s->execute([(int)$parts[1]]);
    $sh = $s->fetch(PDO::FETCH_ASSOC);
    $sh ? out($sh) : out(['error' => 'not found'], 404);
}

// GET /shipments?q= -> API4 unrestricted resource consumption + SQLi
if ($parts[0] === 'shipments') {
    if (!$me) out(['error' => 'auth required'], 401);
    $qs = $_GET['q'] ?? '';
    $sql = "SELECT tracking,origin,destination,status FROM shipments WHERE destination LIKE '%$qs%'";
    out(['results' => q_raw($sql)->fetchAll(PDO::FETCH_ASSOC)]);
}

// GET /admin/users -> API5 broken function level authorization
if ($parts[0] === 'admin' && ($parts[1] ?? '') === 'users') {
    if (!$me) out(['error' => 'auth required'], 401);
    out(['flag' => pf('bfla'),
         'users' => q_raw("SELECT id,username,role,api_token,ssn FROM users")->fetchAll(PDO::FETCH_ASSOC)]);
}

// GET /import?url= -> API7 SSRF / API10 unsafe consumption
if ($parts[0] === 'import') {
    if (!$me) out(['error' => 'auth required'], 401);
    $body = @file_get_contents($_GET['url'] ?? '');
    out(['fetched' => $_GET['url'] ?? '', 'body' => $body]);
}

out(['error' => 'unknown endpoint'], 404);
