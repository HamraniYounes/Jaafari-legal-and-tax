<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_admin();

$error = $success = '';
const YOUTUBE_PATTERN = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('Identifiant invalide.');
}

$stmt = $pdo->prepare('SELECT * FROM videos WHERE id = ?');
$stmt->execute([$id]);
$video = $stmt->fetch();

if (!$video) {
    http_response_code(404);
    exit('Vidéo non trouvée.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $title = trim(mb_substr((string) ($_POST['title'] ?? ''), 0, 255));
    $url   = trim((string) ($_POST['url'] ?? ''));

    if ($title === '' || $url === '') {
        $error = "Le titre et l'URL sont obligatoires.";
    } elseif (!preg_match(YOUTUBE_PATTERN, $url)) {
        $error = "Veuillez entrer un lien YouTube valide. Formats acceptés :<br>
                  • <code>https://www.youtube.com/watch?v=abc123def45</code><br>
                  • <code>https://youtu.be/abc123def45</code><br>
                  • <code>https://www.youtube.com/shorts/abc123def45</code>";
    } else {
        $stmt = $pdo->prepare('UPDATE videos SET title = ?, url = ? WHERE id = ?');
        if ($stmt->execute([$title, $url, $id])) {
            $success = 'Vidéo mise à jour avec succès.';
        } else {
            $error = 'Erreur lors de la mise à jour.';
        }
    }
}

include __DIR__ . '/../includes/header-admin.php';
?>

<div class="container py-5">
    <h2 class="section-title">Modifier la Vidéo YouTube</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error /* contient du HTML intentionnel */ ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
        <a href="dashboard.php" class="btn btn-orange">Retour au tableau de bord</a>
    <?php else: ?>
        <form method="POST" action="edit_video.php?id=<?= (int) $id ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Titre de la vidéo</label>
                <input type="text" name="title" class="form-control"
                       value="<?= e($video['title']) ?>"
                       placeholder="Ex : Comprendre le droit des étrangers"
                       required maxlength="255">
            </div>

            <div class="mb-3">
                <label class="form-label">Lien YouTube</label>
                <input type="url" name="url" class="form-control"
                       value="<?= e($video['url']) ?>"
                       placeholder="https://www.youtube.com/watch?v=..." required>
                <small class="text-muted">
                    Liens acceptés :<br>
                    • <code>youtube.com/watch?v=...</code><br>
                    • <code>youtu.be/...</code><br>
                    • <code>youtube.com/shorts/...</code>
                </small>
            </div>

            <button type="submit" class="btn btn-primary">Mettre à jour la vidéo</button>
        </form>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
