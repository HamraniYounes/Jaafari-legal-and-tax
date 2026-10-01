<?php
declare(strict_types=1);

/** Jetons CSRF : protection contre la falsification de requêtes inter-sites. */

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

/** Champ caché à insérer dans chaque formulaire POST. */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

/** À appeler en haut de chaque traitement POST. Arrête le script si le jeton est absent/invalide. */
function csrf_verify(): void
{
    $ok = isset($_POST['_csrf'], $_SESSION['_csrf'])
        && is_string($_POST['_csrf'])
        && hash_equals($_SESSION['_csrf'], $_POST['_csrf']);
    if (!$ok) {
        http_response_code(419);
        exit('Requête invalide (jeton CSRF). Rechargez la page et réessayez.');
    }
}
