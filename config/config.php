<?php
declare(strict_types=1);

/**
 * Chargement central de la configuration.
 * Les secrets se trouvent dans /.env (jamais versionné — voir .gitignore).
 */

const APP_ROOT = __DIR__ . '/..';

// --- Parseur de fichier .env (sans dépendance externe) ---
function load_env(string $path): void
{
    if (!is_file($path)) {
        return;
    }
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$name, $value] = explode('=', $line, 2);
        $name  = trim($name);
        $value = trim($value);
        if ((str_starts_with($value, '"') && str_ends_with($value, '"'))
            || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }
        if (getenv($name) === false) {
            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
        }
    }
}

function env(string $key, ?string $default = null): ?string
{
    $value = $_ENV[$key] ?? getenv($key);
    return ($value === false || $value === null) ? $default : (string) $value;
}

load_env(APP_ROOT . '/.env');

// --- Application ---
define('APP_ENV', env('APP_ENV', 'production'));
define('SITE_URL', rtrim(env('SITE_URL', 'https://jaafari.be'), '/'));
define('SITE_NAME', 'JAAFARI Legal & Tax');

// --- Base de données ---
define('DB_HOST', env('DB_HOST', ''));
define('DB_NAME', env('DB_NAME', ''));
define('DB_USER', env('DB_USER', ''));
define('DB_PASS', env('DB_PASS', ''));

// --- SMTP ---
define('SMTP_HOST', env('SMTP_HOST', 'smtp.ionos.fr'));
define('SMTP_PORT', (int) env('SMTP_PORT', '587'));
define('SMTP_USER', env('SMTP_USER', ''));
define('SMTP_PASS', env('SMTP_PASS', ''));
define('MAIL_FROM', env('MAIL_FROM', SMTP_USER));
define('MAIL_FROM_NAME', env('MAIL_FROM_NAME', SITE_NAME));
define('MAIL_TO', env('MAIL_TO', SMTP_USER));

// --- Affichage des erreurs : RIEN en production, tout en dev ---
if (APP_ENV === 'production') {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}
