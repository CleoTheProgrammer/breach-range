<?php
require_once __DIR__ . '/../../db.php';
header('Content-Type: application/json');
header("Content-Security-Policy: default-src 'none'");

// Fixed authentication: token from a header only (never the query string), and in
// production these are short-lived and rotated.
function current_user() {
    $tok = $_SERVER['HTTP_X_API_TOKEN'] ?? '';
    if ($tok === '') return null;
    $s = db()->prepare("SELECT * FROM users WHERE api_token = ?");
    $s->execute([$tok]);
    return $s->fetch(PDO::FETCH_ASSOC) ?: null;
}
function out($data, $code = 200) { http_response_code($code); echo json_encode($data, JSON_PRETTY_PRINT); exit; }
function public_user($u) {
    return ['id'=>$u['id'],'username'=>$u['username'],'full_name'=>$u['full_name'],'email'=>$u['email'],'role'=>$u['role']];
}

$uri   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path  = trim(preg_replace('#^.*/api/v1#', '', $uri), '/');
$parts = $path === '' ? [] : explode('/', $path);
$me    = current_user();
if (!$me && $parts) { out(['error' => 'authentication required'], 401); }

if (!$parts) {
    out(['service' => 'Kestrel Partner API', 'version' => 'v1',
         'endpoints' => ['/whoami', '/users/{id}', '/shipments/{id}', '/shipments?q=&limit=',
                         '/admin/users', '/orders/bulk?count=', '/import?url=']]);
}

// GET /whoami -> token only ever read from the header, so it is not leaked in URLs/logs.
if ($parts[0] === 'whoami') { out(public_user($me)); }

// GET /users/{id} -> self or admin, minimal fields.
if ($parts[0] === 'users' && isset($parts[1]) && ctype_digit($parts[1]) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = (int)$parts[1];
    if ($id !== (int)$me['id'] && $me['role'] !== 'admin') out(['error' => 'forbidden'], 403);
    $s = db()->prepare("SELECT * FROM users WHERE id = ?"); $s->execute([$id]);
    $u = $s->fetch(PDO::FETCH_ASSOC);
    $u ? out(public_user($u)) : out(['error' => 'not found'], 404);
}

// PATCH /users/{id} -> self or admin, allow-listed fields.
if ($parts[0] === 'users' && isset($parts[1]) && $_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $id = (int)$parts[1];
    if ($id !== (int)$me['id'] && $me['role'] !== 'admin') out(['error' => 'forbidden'], 403);
    $in = json_decode(file_get_contents('php://input'), true) ?: [];
    $allowed = ['full_name','email','phone']; $sets = []; $vals = [];
    foreach ($allowed as $k) { if (array_key_exists($k, $in)) { $sets[] = "$k = ?"; $vals[] = $in[$k]; } }
    if (!$sets) out(['error' => 'no writable fields'], 400);
    $vals[] = $id;
    db()->prepare("UPDATE users SET ".implode(',', $sets)." WHERE id = ?")->execute($vals);
    out(['updated' => true]);
}

// GET /shipments/{id} -> ownership or admin.
if ($parts[0] === 'shipments' && isset($parts[1]) && ctype_digit($parts[1])) {
    $id = (int)$parts[1];
    if ($me['role'] === 'admin') { $s = db()->prepare("SELECT * FROM shipments WHERE id = ?"); $s->execute([$id]); }
    else { $s = db()->prepare("SELECT * FROM shipments WHERE id = ? AND owner_id = ?"); $s->execute([$id, $me['id']]); }
    $sh = $s->fetch(PDO::FETCH_ASSOC);
    $sh ? out($sh) : out(['error' => 'not found'], 404);
}

// GET /shipments?q=&limit= -> parameterised and capped pagination (API4 fixed).
if ($parts[0] === 'shipments') {
    $q = '%' . ($_GET['q'] ?? '') . '%';
    $limit = min(50, max(1, (int)($_GET['limit'] ?? 20)));
    $offset = max(0, (int)($_GET['offset'] ?? 0));
    $s = db()->prepare("SELECT tracking,origin,destination,status FROM shipments
                        WHERE owner_id = ? AND destination LIKE ? LIMIT $limit OFFSET $offset");
    $s->execute([$me['id'], $q]);
    out(['results' => $s->fetchAll(PDO::FETCH_ASSOC), 'limit' => $limit, 'offset' => $offset]);
}

// GET /admin/users -> admins only.
if ($parts[0] === 'admin' && ($parts[1] ?? '') === 'users') {
    if ($me['role'] !== 'admin') out(['error' => 'forbidden'], 403);
    out(['users' => db()->query("SELECT id,username,full_name,email,role FROM users")->fetchAll(PDO::FETCH_ASSOC)]);
}

// GET /orders/bulk?count= -> capped quantity + anti-automation note (API6 fixed).
if ($parts[0] === 'orders' && ($parts[1] ?? '') === 'bulk') {
    $count = (int)($_GET['count'] ?? 1);
    if ($count < 1 || $count > 50) out(['error' => 'count must be between 1 and 50'], 400);
    out(['created' => $count, 'status' => 'queued']);
}

// No /config debug endpoint in production (API8 fixed). CORS is same-origin (.htaccess).

// GET /import?url= -> SSRF-safe fetch.
if ($parts[0] === 'import') {
    $url = $_GET['url'] ?? '';
    $p = parse_url($url);
    if (!$p || !in_array(strtolower($p['scheme'] ?? ''), ['http','https'], true) || empty($p['host']))
        out(['error' => 'only http(s) URLs allowed'], 400);
    foreach ((@gethostbynamel($p['host']) ?: []) as $ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE))
            out(['error' => 'destination is private or reserved'], 400);
    }
    $ctx = stream_context_create(['http' => ['timeout' => 4, 'max_redirects' => 0]]);
    out(['fetched' => $url, 'body' => @file_get_contents($url, false, $ctx)]);
}

out(['error' => 'unknown endpoint'], 404);
