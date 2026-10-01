<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/articles.php';
require_once __DIR__ . '/config/db.php';

$article = null;

// 1) URL propre : /mon-titre-de-l-article (réécrite par .htaccess vers article.php?slug=...)
if (!empty($_GET['slug']) && is_string($_GET['slug'])) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM articles WHERE slug = ? LIMIT 1');
        $stmt->execute([strtolower($_GET['slug'])]);
        $article = $stmt->fetch() ?: null;
    } catch (Throwable $e) {
        $article = null;
    }

// 2) Ancienne URL : article.php?id=12 -> redirection 301 vers l'URL propre
} elseif (!empty($_GET['id']) && is_numeric($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM articles WHERE id = ?');
    $stmt->execute([(int) $_GET['id']]);
    $article = $stmt->fetch() ?: null;

    if ($article) {
        $slug = article_slug($pdo, $article);
        if ($slug) {
            header('Location: ' . SITE_URL . '/' . $slug, true, 301);
            exit;
        }
    }
}

// Article introuvable : vrai code 404
if (!$article) {
    http_response_code(404);
    $page_title   = 'Page introuvable | JAAFARI Legal & Tax';
    $page_noindex = true;
    include __DIR__ . '/includes/header.php';
    ?>
    <div class="container py-5">
        <h1>Page introuvable</h1>
        <p>Cette page n'existe pas ou a été déplacée.</p>
        <a href="actualites.php" class="btn btn-secondary btn-sm">&larr; Retour aux actualités</a>
    </div>
    <?php
    include __DIR__ . '/includes/footer.php';
    exit;
}

// --- SEO : variables lues par includes/seo.php (dans header.php) ---
$plainText = html_entity_decode(strip_tags($article['content']), ENT_QUOTES, 'UTF-8');
$plainText = trim(preg_replace('/\s+/', ' ', $plainText));

$page_title       = mb_substr($article['title'], 0, 60) . ' | JAAFARI Legal & Tax';
$page_description = mb_strlen($plainText) > 155 ? mb_substr($plainText, 0, 152) . '...' : $plainText;
$page_type        = 'article';
$page_canonical   = article_url($pdo, $article, true);
$article_meta     = [
    'published' => date('c', strtotime($article['created_at'])),
    'author'    => $article['author'],
];

if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $article['content'], $m)
    && stripos($m[1], 'data:') !== 0) {
    $page_image = $m[1];
}

include __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <a href="actualites.php" class="btn btn-secondary btn-sm mb-3">&larr; Retour aux actualités</a>

    <h1><?= e($article['title']) ?></h1>

    <p>
        <small class="text-muted">
            Par <strong><?= e($article['author']) ?></strong>,
            publié le <time datetime="<?= date('c', strtotime($article['created_at'])) ?>"><?= e(format_date($article['created_at'])) ?></time>
        </small>
    </p>

    <hr>

    <div class="article-content mt-4">
        <?= sanitize_html($article['content']) ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
