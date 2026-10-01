<?php
declare(strict_types=1);

/** Échappement HTML raccourci (à utiliser pour TOUTE sortie). */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/** Redirection HTTP + arrêt immédiat du script. */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/** Date formatée "16/08/2025 à 21h18" (compatible PHP 8.1+, remplace strftime déprécié). */
function format_date(string $dateString): string
{
    $ts = strtotime($dateString);
    return $ts ? date('d/m/Y \à H\hi', $ts) : '';
}

/**
 * Nettoyage HTML minimal côté AFFICHAGE (defense in depth — l'admin reste
 * de confiance, mais un compte compromis ne doit pas pouvoir servir du
 * JavaScript aux visiteurs).
 */
function sanitize_html(string $html): string
{
    $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html) ?? '';
    $html = preg_replace('#<iframe\b[^>]*>.*?</iframe>#is', '', $html) ?? '';
    $html = preg_replace('#<object\b[^>]*>.*?</object>#is', '', $html) ?? '';
    $html = preg_replace('#<embed\b[^>]*>#is', '', $html) ?? '';
    // Attributs de gestionnaires d'événements : onclick="…", onerror=…, etc.
    $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? '';
    // URLs javascript: dans href/src
    $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1=$2#$2', $html) ?? '';
    return $html;
}
