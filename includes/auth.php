<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../config/db.php';

const MAX_LOGIN_ATTEMPTS  = 5;
const LOGIN_LOCK_SECONDS  = 300;   // 5 minutes de blocage après 5 échecs
const ADMIN_IDLE_TIMEOUT  = 1800;  // déconnexion après 30 min d'inactivité

function is_admin_logged_in(): bool
{
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/** Protège une page admin : redirige vers le login si besoin, gère le timeout d'inactivité. */
function require_admin(): void
{
    if (!is_admin_logged_in()) {
        redirect('/admin/login.php');
    }
    if (!empty($_SESSION['admin_last_activity'])
        && time() - (int) $_SESSION['admin_last_activity'] > ADMIN_IDLE_TIMEOUT) {
        logout_admin();
        redirect('/admin/login.php?timeout=1');
    }
    $_SESSION['admin_last_activity'] = time();
}

/** Anti brute-force : true si une tentative est autorisée maintenant. */
function login_attempt_allowed(): bool
{
    return time() >= (int) ($_SESSION['login_locked_until'] ?? 0);
}

function login_lock_remaining(): int
{
    return max(0, (int) ($_SESSION['login_locked_until'] ?? 0) - time());
}

function record_failed_login(): void
{
    $_SESSION['login_attempts'] = (int) ($_SESSION['login_attempts'] ?? 0) + 1;
    if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
        $_SESSION['login_locked_until'] = time() + LOGIN_LOCK_SECONDS;
        $_SESSION['login_attempts'] = 0;
    }
}

function record_successful_login(): void
{
    unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
    session_regenerate_id(true); // prévient la fixation de session
    $_SESSION['admin_logged_in']     = true;
    $_SESSION['admin_last_activity'] = time();
}

function logout_admin(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/**
 * Vérifie un couple identifiant/mot de passe contre la table `admin`.
 * Retourne true en cas de succès. Message volontairement générique.
 */
function attempt_login(string $username, string $password): bool
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM admin WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password'])) {
        record_successful_login();
        return true;
    }
    record_failed_login();
    return false;
}
