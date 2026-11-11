<?php
require_once __DIR__ . '/config.php';
function db() {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
             PDO::ATTR_EMULATE_PREPARES => true] // emulation on: lets stacked queries through
        );
    }
    return $pdo;
}
// Raw query helper used by the deliberately vulnerable pages.
function q_raw($sql) { return db()->query($sql); }
