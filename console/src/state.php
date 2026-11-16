<?php
// Lightweight public endpoint students poll to learn if challenges are released.
require_once __DIR__ . '/helpers.php';
header('Content-Type: application/json');
header('Cache-Control: no-store');
echo json_encode(['visible' => challenges_visible() ? 1 : 0]);
