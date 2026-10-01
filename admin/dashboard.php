<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_admin();

$articles = $pdo->query('SELECT * FROM articles ORDER BY created_at DESC')->fetchAll();
$videos   = $pdo->query('SELECT * FROM videos ORDER BY created_at DESC')->fetchAll();

include __DIR__ . '/../includes/header-admin.php';
?>

<div class="container py-5">
    <h2 class="section-title">Tableau de Bord Admin</h2>

    <div class="admin-panel">
        <a href="add_article.php" class="btn btn-primary mb-3">+ Nouvel Article</a>
        <h5>Articles</h5>
        <ul class="list-group mb-4">
            <?php foreach ($articles as $a): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= e($a['title']) ?></strong><br>
                    <small class="text-muted"> Publié le <?= e(format_date($a['created_at'])) ?></small>
                </div>
                <span class="d-flex gap-1">
                    <a href="edit_article.php?id=<?= (int) $a['id'] ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
                    <!-- Suppression en POST + CSRF : impossible à déclencher par simple visite d'un lien -->
                    <form method="POST" action="delete_article.php" onsubmit="return confirm('Supprimer cet article ?');" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">X</button>
                    </form>
                </span>
            </li>
            <?php endforeach; ?>
        </ul>

        <a href="add_video.php" class="btn btn-primary mb-3">+ Nouvelle Vidéo</a>
        <h5>Vidéos</h5>
        <ul class="list-group">
            <?php foreach ($videos as $v): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= e($v['title']) ?></strong><br>
                    <small class="text-muted"> Publié le <?= e(format_date($v['created_at'])) ?></small>
                </div>
                <span class="d-flex gap-1">
                    <a href="edit_video.php?id=<?= (int) $v['id'] ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
                    <form method="POST" action="delete_video.php" onsubmit="return confirm('Supprimer cette vidéo ?');" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $v['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">X</button>
                    </form>
                </span>
            </li>
            <?php endforeach; ?>
        </ul>

        <a href="logout.php" class="btn btn-secondary mt-4">Déconnexion</a>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
