<?php
declare(strict_types=1);

/**
 * Amorce commune à TOUTES les pages (publiques et admin) :
 * config, session sécurisée, en-têtes HTTP de sécurité.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

// --- Session sécurisée ---
// Cookie HttpOnly (invisible en JS => mitigation XSS), SameSite=Lax,
// Secure uniquement si HTTPS (évite de casser le site en local HTTP).
if (session_status() === PHP_SESSION_NONE) {
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// --- En-têtes de sécurité (doublon volontaire avec le .htaccess :
//     ces en-têtes s'appliquent aussi si .htaccess est ignoré) ---
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
if (APP_ENV === 'production' && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}
