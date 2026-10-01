<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/articles.php';
require_once __DIR__ . '/config/db.php';

// Fonction pour tronquer le HTML sans casser les balises
function truncateHtml($html, $maxLength = 150) {
    $printedLength = 0;
    $tags = [];
    $truncated = '';

    preg_match_all('/<[^>]+>|[^<]+/', $html, $matches, PREG_OFFSET_CAPTURE);

    foreach ($matches[0] as $match) {
        $str = $match[0];

        if ($str[0] === '<') {
            if ($str[1] !== '/') {
                preg_match('/<(\w+)/', $str, $tagName);
                $tags[] = $tagName[1];
            } else {
                array_pop($tags);
            }
            $truncated .= $str;
        } else {
            if ($printedLength + strlen($str) > $maxLength) {
                $truncated .= substr($str, 0, $maxLength - $printedLength);
                $printedLength = $maxLength;
                break;
            } else {
                $truncated .= $str;
                $printedLength += strlen($str);
            }
        }
    }

    while (!empty($tags)) {
        $truncated .= '</' . array_pop($tags) . '>';
    }

    return $truncated . '...';
}

// 🔹 Gestion du tri — VALIDATION STRICTE (whitelist)
$allowedSort = ['asc', 'desc'];

$articleSort = in_array($_GET['article_sort'] ?? '', $allowedSort, true)
    ? $_GET['article_sort']
    : 'desc';

$videoSort = in_array($_GET['video_sort'] ?? '', $allowedSort, true)
    ? $_GET['video_sort']
    : 'desc';

// 🔹 Récupération des articles avec tri dynamique (valeurs whitelistées : sûr)
$articles = $pdo->query("SELECT * FROM articles ORDER BY created_at {$articleSort}")->fetchAll();
$videos   = $pdo->query("SELECT * FROM videos ORDER BY created_at {$videoSort}")->fetchAll();

// Fonction pour les vidéos YouTube (supporte les Shorts)
function getYouTubeEmbed($url) {
    $pattern = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
    if (preg_match($pattern, $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }
    return false;
}

// 🔹 Helper pour générer l'URL avec tri + onglet préservé
function buildSortUrl($sortKey, $sortValue, $forceTab = null) {
    $params = $_GET;
    unset($params['article_sort'], $params['video_sort']);

    if ($forceTab) {
        $params['tab'] = $forceTab;
    }

    $params[$sortKey] = $sortValue;
    return '?' . http_build_query($params);
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<style>
    .card.article-card { background-color: #f5aa5d; border: none; border-radius: 12px; box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1); transition: transform 0.2s ease; color: #1a3a5f; height: 100%; }
    .card.article-card:hover { transform: translateY(-4px); }
    .card.article-card a { color: #1a3a5f; }
    .card.article-card a:hover { color: #da6600; }
    .card.article-card .text-muted { color: #1a3a5f !important; }
    .card.article-card .btn-outline-primary,
    .card.article-card .btn-primary { background-color: #da6600; color: white; border: none; font-weight: 600; }
    .card.article-card .btn-outline-primary:hover,
    .card.article-card .btn-primary:hover { background-color: #ea580c; color: white; }
    #searchArticles, #searchVideos { border-radius: 50px; border: 1px solid #ddd; }
    .nav-tabs .nav-link { border: none; color: #555; }
    .nav-tabs .nav-link.active { color: #da6600; border-bottom: 3px solid #da6600; }
    .sort-controls .btn { font-size: 0.85rem; padding: 0.25rem 0.75rem; }
    .sort-controls .btn.active { background-color: #da6600; color: white; border-color: #da6600; font-weight: 600; }
    .ratio-9x16 { --bs-aspect-ratio: 56.25%; }
    .video-item .ratio-9x16 { max-width: 700px; width: 100%; margin: 0 auto; }
    @media (min-width: 992px) { .video-item .ratio-16x9 { height: 350px; } }
</style>

<div style="font-family: 'Lato', sans-serif;" class="container py-5">
    <h1 class="section-title" style="color: #da6600;"><?= e($lang['news_title']) ?></h1>

    <!-- Onglets -->
    <ul class="nav nav-tabs mb-4" id="contentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="articles-tab" data-bs-toggle="tab" data-bs-target="#articles" type="button" role="tab">
                <?= e($lang['tab_articles']) ?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="videos-tab" data-bs-toggle="tab" data-bs-target="#videos" type="button" role="tab">
                <?= e($lang['tab_videos']) ?>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="contentTabsContent">

        <!-- Onglet Articles -->
        <div class="tab-pane fade show active" id="articles" role="tabpanel">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <input type="text" class="form-control" id="searchArticles"
                       placeholder="<?= e($lang['search_articles']) ?>"
                       oninput="filterArticles()"
                       style="max-width: 300px;">

                <div class="sort-controls btn-group" role="group" aria-label="Tri des articles">
                    <a href="<?= e(buildSortUrl('article_sort', 'asc')) ?>"
                       class="btn btn-outline-secondary <?= $articleSort === 'asc' ? 'active' : '' ?>">
                         Date Asc
                    </a>
                    <a href="<?= e(buildSortUrl('article_sort', 'desc')) ?>"
                       class="btn btn-outline-secondary <?= $articleSort === 'desc' ? 'active' : '' ?>">
                         Date Desc
                    </a>
                </div>
            </div>

            <div class="row" id="articlesContainer">
                <?php if (empty($articles)): ?>
                    <div class="col-12">
                        <div class="alert alert-info"><?= e($lang['no_articles']) ?></div>
                    </div>
                <?php else: ?>
                    <?php foreach ($articles as $article): ?>
                        <?php $articleUrl = e(article_url($pdo, $article)); ?>
                        <div class="col-md-6 mb-4 article-item">
                            <div class="card article-card">
                                <div class="card-body">
                                    <h5 class="mb-1">
                                        <a href="<?= $articleUrl ?>" class="text-decoration-none" target="_blank" rel="noopener">
                                            <?= e($article['title']) ?>
                                        </a>
                                    </h5>
                                    <p class="small text-muted mb-2">
                                        <?= e($lang['by']) ?><strong><?= e($article['author']) ?></strong>
                                    </p>
                                    <small class="text-muted"><?= e($lang['published_on']) ?> <?= e(format_date($article['created_at'])) ?></small>
                                    <br>
                                    <a href="<?= $articleUrl ?>" class="btn btn-sm btn-primary mt-2" target="_blank" rel="noopener"><?= e($lang['read_more']) ?></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Onglet Vidéos -->
        <div class="tab-pane fade" id="videos" role="tabpanel">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <input type="text" class="form-control" id="searchVideos"
                       placeholder="<?= e($lang['search_videos']) ?>"
                       oninput="filterVideos()"
                       style="max-width: 300px;">

                <div class="sort-controls btn-group" role="group" aria-label="Tri des vidéos">
                    <a href="<?= e(buildSortUrl('video_sort', 'asc', 'videos')) ?>"
                       class="btn btn-outline-secondary <?= $videoSort === 'asc' ? 'active' : '' ?>">
                         Date Asc
                    </a>
                    <a href="<?= e(buildSortUrl('video_sort', 'desc', 'videos')) ?>"
                       class="btn btn-outline-secondary <?= $videoSort === 'desc' ? 'active' : '' ?>">
                         Date Desc
                    </a>
                </div>
            </div>

            <div class="row" id="videosContainer">
                <?php if (empty($videos)): ?>
                    <div class="col-12">
                        <div class="alert alert-info"><?= e($lang['no_videos']) ?></div>
                    </div>
                <?php else: ?>
                    <?php foreach ($videos as $video): ?>
                        <?php $embedUrl = getYouTubeEmbed($video['url']); ?>
                        <?php if ($embedUrl): ?>
                            <?php $isShort = strpos($video['url'], '/shorts/') !== false; ?>
                            <div class="col-12 col-lg-6 mb-4 video-item">
                                <div class="ratio <?= $isShort ? 'ratio-9x16' : 'ratio-16x9' ?>">
                                    <iframe
                                        src="<?= e($embedUrl) ?>?rel=0"
                                        title="<?= e($video['title'] ?: 'Vidéo YouTube') ?>"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                        loading="lazy">
                                    </iframe>
                                </div>
                                <h6 class="mt-2"><?= e($video['title']) ?></h6>
                                <small class="text-muted"><?= e($lang['published_on']) ?> <?= e(format_date($video['created_at'])) ?></small>
                            </div>
                        <?php else: ?>
                            <div class="col-12 col-lg-6 mb-4 video-item">
                                <div class="alert alert-warning"><?= e($lang['invalid_youtube']) ?></div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function filterArticles() {
    const input = document.getElementById('searchArticles');
    const filter = input.value.toLowerCase();
    const items = document.getElementsByClassName('article-item');
    for (let i = 0; i < items.length; i++) {
        const title = items[i].querySelector('h5 a')?.textContent.toLowerCase() || '';
        const content = items[i].querySelector('p')?.textContent.toLowerCase() || '';
        items[i].style.display = (title.includes(filter) || content.includes(filter)) ? '' : 'none';
    }
}

function filterVideos() {
    const input = document.getElementById('searchVideos');
    const filter = input.value.toLowerCase();
    const items = document.getElementsByClassName('video-item');
    for (let i = 0; i < items.length; i++) {
        const title = items[i].querySelector('h6')?.textContent.toLowerCase() || '';
        items[i].style.display = title.includes(filter) ? '' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'videos') {
        const triggerEl = document.querySelector('#videos-tab');
        if (triggerEl && typeof bootstrap !== 'undefined') {
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        }
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
