<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 60 * 60 * 24,
        'cookie_secure' => true,    // Только HTTPS
        'cookie_httponly' => true,  // Защита от XSS
        'use_strict_mode' => true   // Блокировка session fixation
    ]);
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}