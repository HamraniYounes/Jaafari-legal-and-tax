<?php
require_once __DIR__ . '/bootstrap.php';

// Langues disponibles
$available_langs = ['fr', 'en', 'nl', 'es', 'ar'];
$default_lang = 'fr';

// Détection de la langue (whitelist stricte)
$lang_code = $default_lang;

if (isset($_GET['lang']) && in_array($_GET['lang'], $available_langs, true)) {
    $_SESSION['lang'] = $_GET['lang'];
    $lang_code = $_GET['lang'];
} elseif (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $available_langs, true)) {
    $lang_code = $_SESSION['lang'];
}

// Charger les traductions
$translations_file = __DIR__ . "/translations/{$lang_code}.php";
if (is_file($translations_file)) {
    require_once $translations_file;
} else {
    require_once __DIR__ . "/translations/{$default_lang}.php";
    $lang_code = $default_lang;
}
?>
<!DOCTYPE html>
<html lang="<?= e($lang_code) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/seo.php'; ?>
    <title><?= e($lang['title']) ?></title>

    <!-- Open Graph / SEO -->
    <meta property="og:title" content="Avocat à Bruxelles | Cabinet JAAFARI Legal &amp; Tax">
    <meta property="og:description" content="Expertise juridique et fiscale à Bruxelles. Droit des sociétés, fiscalité, immobilier, pénal. Contactez-nous pour un conseil personnalisé.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e(SITE_URL) ?>/?lang=<?= e($lang_code) ?>">
    <meta property="og:image" content="<?= e(SITE_URL) ?>/media/Logo-Walid-New.png">
    <meta property="og:site_name" content="<?= e(SITE_NAME) ?>">

    <!-- SEO Multilingue -->
    <link rel="alternate" hreflang="fr" href="<?= e(SITE_URL) ?>/?lang=fr">
    <link rel="alternate" hreflang="en" href="<?= e(SITE_URL) ?>/?lang=en">
    <link rel="alternate" hreflang="nl" href="<?= e(SITE_URL) ?>/?lang=nl">
    <link rel="alternate" hreflang="x-default" href="<?= e(SITE_URL) ?>/">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <!-- Style personnalisé -->
    <link href="assets/css/style.css" rel="stylesheet">

    <style>
        html { font-family: 'Lato', sans-serif; }
        .nav-link:hover { border-bottom: 2px solid #da6600; }
        .navbar-brand img { width: 140px; height: 140px; object-fit: contain; }
        .top-bar { background-color: #f5aa5d; height: 25px; width: 100%; }
        @media (max-width: 991.98px) {
            .navbar-nav .nav-link { text-align: center; }
        }
        .language-switcher { margin-left: 10px; }
        .language-switcher .dropdown-toggle {
            background: none; border: none; color: #da6600;
            font-weight: 600; font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <!-- Bande supérieure orange -->
    <div class="top-bar"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-0">
        <div class="container-fluid">

            <!-- Logo à gauche -->
            <a class="navbar-brand" href="index.php?lang=<?= e($lang_code) ?>">
                <img src="media/Logo-Walid-New.png" alt="Logo JAAFARI">
            </a>

            <!-- Bouton burger -->
            <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu déroulant -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">

                    <li class="nav-item">
                        <a class="nav-link active" href="index.php?lang=<?= e($lang_code) ?>">
                            <?= e($lang['nav_accueil']) ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="services.php?lang=<?= e($lang_code) ?>">
                            <?= e($lang['nav_services']) ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="actualites.php?lang=<?= e($lang_code) ?>">
                            <?= e($lang['nav_actualites']) ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="contact.php?lang=<?= e($lang_code) ?>">
                            <?= e($lang['nav_contact']) ?>
                        </a>
                    </li>

                    <!-- Sélecteur de langue -->
                    <li class="nav-item language-switcher">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= e(strtoupper($lang_code)) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="?lang=fr">Français</a></li>
                                <li><a class="dropdown-item" href="?lang=en">English</a></li>
                                <li><a class="dropdown-item" href="?lang=nl">Nederlands</a></li>
                                <li><a class="dropdown-item" href="?lang=es">Español</a></li>
                            </ul>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
