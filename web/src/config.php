<?php
// Central config for Kestrel Freight portal (Stage 1 lab build).
session_start();
define('APP_STAGE', getenv('APP_STAGE') ?: '1');
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_NAME', getenv('DB_NAME') ?: 'kestrel');
define('DB_USER', getenv('DB_USER') ?: 'kestrel_app');
define('DB_PASS', getenv('DB_PASS') ?: 'Summer2023!');
