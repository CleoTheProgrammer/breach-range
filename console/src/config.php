<?php
session_start();
date_default_timezone_set('Africa/Johannesburg');
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_NAME', getenv('DB_NAME') ?: 'console');
define('DB_USER', getenv('DB_USER') ?: 'console_app');
define('DB_PASS', getenv('DB_PASS') ?: 'console-strong-pw');
define('TARGET_URL', getenv('TARGET_URL') ?: 'http://127.0.0.1:8080');
define('FLAG_PEPPER', getenv('FLAG_PEPPER') ?: 'dev-pepper');
define('PTS_FLAG', 100);
define('PTS_QUIZ', 50);
define('INSTRUCTOR_PASSWORD', getenv('INSTRUCTOR_PASSWORD') ?: 'change-me-instructor');
