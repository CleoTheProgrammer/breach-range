<?php
require_once __DIR__ . '/config.php';
function db() {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
            DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }
    return $pdo;
}
function current_player() {
    if (empty($_SESSION['pid'])) return null;
    $s = db()->prepare("SELECT * FROM players WHERE id = ?");
    $s->execute([$_SESSION['pid']]);
    return $s->fetch(PDO::FETCH_ASSOC) ?: null;
}
function require_player() {
    $p = current_player();
    if (!$p) { header('Location: /index.php'); exit; }
    return $p;
}
