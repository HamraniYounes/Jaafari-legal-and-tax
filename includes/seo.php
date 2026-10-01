<?php
/**
 * includes/seo.php  (anciennement Seo.php — renommé en minuscules pour
 * éviter les problèmes de casse sur les serveurs Linux)
 * À inclure DANS le <head> de includes/header.php.
 *
 * Chaque page peut définir AVANT l'include du header :
 *   $page_title, $page_description, $page_image, $page_type ('website' | 'article'),
 *   $page_canonical, $page_noindex (bool), $article_meta ['published' => ..., 'author' => ...]
 */

$siteUrl  = SITE_URL;
$siteName = SITE_NAME;
$page     = basename($_SERVER['SCRIPT_NAME'], '.php');

$defaults = [
    'index' => [
        'Avocat fiscaliste à Bruxelles | JAAFARI Legal & Tax',
        "Me Walid Jaafari, avocat à Bruxelles : droit fiscal, patrimonial, pénal et droit des étrangers. Une approche rigoureuse, créative et confidentielle.",
    ],
    'services' => [
        "Droit fiscal, patrimonial, pénal et des étrangers | JAAFARI",
        "Découvrez les domaines d'intervention du cabinet JAAFARI Legal & Tax à Bruxelles : droit fiscal, droit patrimonial, droit pénal et droit des étrangers.",
    ],
    'actualites' => [
        "Actualités juridiques et fiscales | JAAFARI Legal & Tax",
        "Articles et vidéos de Me Walid Jaafari, avocat fiscaliste à Bruxelles : l'actualité du droit fiscal, patrimonial et pénal expliquée simplement.",
    ],
    'contact' => [
        "Contact - Avocat à Bruxelles | JAAFARI Legal & Tax",
        "Contactez Me Walid Jaafari, avocat à Bruxelles : avenue des Sept Bonniers 72, 1180 Bruxelles. Tél. +32 487 52 80 22 - walid@jaafari.be.",
    ],
];

$d = $defaults[$page] ?? $defaults['index'];

$title       = $page_title       ?? ($lang["seo_title_$page"] ?? $d[0]);
$description = $page_description ?? ($lang["seo_desc_$page"]  ?? $d[1]);

$type   = $page_type ?? 'website';
$imgSrc = $page_image ?? 'media/photo_2_crop.jpg';
$image  = preg_match('#^https?://#i', $imgSrc)
    ? $imgSrc
    : $siteUrl . '/' . ltrim($imgSrc, '/');

if (isset($page_canonical)) {
    $canonical = $page_canonical;
} elseif ($page === 'index') {
    $canonical = $siteUrl . '/';
} else {
    $canonical = $siteUrl . '/' . $page . '.php';
}

$noindex = !empty($page_noindex);

$org = [
    '@context'  => 'https://schema.org',
    '@type'     => 'LegalService',
    '@id'       => $siteUrl . '/#cabinet',
    'name'      => $siteName,
    'url'       => $siteUrl . '/',
    'logo'      => $siteUrl . '/media/Logo-Walid.png',
    'image'     => $siteUrl . '/media/photo_2_crop.jpg',
    'telephone' => '+32 487 52 80 22',
    'email'     => 'walid@jaafari.be',
    'address'   => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => 'Avenue des Sept Bonniers, 72',
        'postalCode'      => '1180',
        'addressLocality' => 'Bruxelles',
        'addressCountry'  => 'BE',
    ],
    'areaServed' => ['@type' => 'City', 'name' => 'Bruxelles'],
    'founder'  => [
        '@type'    => 'Person',
        'name'     => 'Walid Jaafari',
        'jobTitle' => 'Avocat',
    ],
    'knowsAbout' => ['Droit fiscal', 'Droit patrimonial', 'Droit pénal', 'Droit des étrangers'],
];

$jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG;
$e = fn($s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?>
<title><?= $e($title) ?></title>
<meta name="description" content="<?= $e($description) ?>">
<link rel="canonical" href="<?= $e($canonical) ?>">
<meta name="robots" content="<?= $noindex ? 'noindex, follow' : 'index, follow, max-image-preview:large' ?>">

<meta property="og:site_name" content="<?= $e($siteName) ?>">
<meta property="og:type" content="<?= $e($type) ?>">
<meta property="og:title" content="<?= $e($title) ?>">
<meta property="og:description" content="<?= $e($description) ?>">
<meta property="og:url" content="<?= $e($canonical) ?>">
<meta property="og:image" content="<?= $e($image) ?>">
<meta property="og:locale" content="fr_BE">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= $e($title) ?>">
<meta name="twitter:description" content="<?= $e($description) ?>">
<meta name="twitter:image" content="<?= $e($image) ?>">

<script type="application/ld+json"><?= json_encode($org, $jsonFlags) ?></script>

<?php if ($type === 'article' && !empty($article_meta)):
    $art = [
        '@context'         => 'https://schema.org',
        '@type'            => 'Article',
        'headline'         => mb_substr($title, 0, 110),
        'description'      => $description,
        'mainEntityOfPage' => $canonical,
        'image'            => $image,
        'datePublished'    => $article_meta['published'],
        'author'           => ['@type' => 'Person', 'name' => $article_meta['author']],
        'publisher'        => ['@id' => $siteUrl . '/#cabinet'],
    ];
?>
<script type="application/ld+json"><?= json_encode($art, $jsonFlags) ?></script>
<?php endif; ?>
