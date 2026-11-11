<?php
// API9 Improper Inventory Management: the old v0 API was never decommissioned.
// NO authentication. Still in production. Pass ?pk= for your personalised flag.
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../flags.php';
header('Content-Type: application/json');
$rows = q_raw("SELECT id,username,full_name,email,phone,role FROM users")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode([
    'warning' => 'deprecated v0 endpoint, scheduled for removal Q3',
    'flag'    => pf('invapi'),
    'users'   => $rows,
], JSON_PRETTY_PRINT);
