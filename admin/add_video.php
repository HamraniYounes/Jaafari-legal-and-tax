<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_admin();

$error = $success = '';
const YOUTUBE_PATTERN = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $title = trim(mb_substr((string) ($_POST['title'] ?? ''), 0, 255));
    $url   = trim((string) ($_POST['url'] ?? ''));

    if ($title === '' || $url === '') {
        $error = "Le titre et l'URL sont obligatoires.";
    } elseif (!preg_match(YOUTUBE_PATTERN, $url)) {
        $error = "Veuillez entrer un lien YouTube valide. Exemples acceptés :<br>
                  • https://www.youtube.com/watch?v=abc123def45<br>
                  • https://youtu.be/abc123def45<br>
                  • https://www.youtube.com/shorts/abc123def45";
    } else {
        $stmt = $pdo->prepare('INSERT INTO videos (title, url) VALUES (?, ?)');
        if ($stmt->execute([$title, $url])) {
            $success = 'Vidéo YouTube (ou Short) ajoutée avec succès.';
        } else {
            $error = "Erreur lors de l'ajout à la base de données.";
        }
    }
}

include __DIR__ . '/../includes/header-admin.php';
?>

<div class="container py-5">
    <h2 class="section-title">Ajouter une Vidéo YouTube</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error /* contient du HTML intentionnel (liste d'exemples) */ ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
        <a href="dashboard.php" class="btn btn-orange">Retour au tableau de bord</a>
    <?php else: ?>
        <form method="POST" action="add_video.php">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Titre de la vidéo</label>
                <input type="text" name="title" class="form-control"
                       placeholder="Ex : Mes conseils sur le droit des étrangers" required maxlength="255">
            </div>

            <div class="mb-3">
                <label class="form-label">Lien YouTube</label>
                <input type="url" name="url" class="form-control"
                       placeholder="https://www.youtube.com/watch?v=..." required>
                <small class="text-muted">
                    Liens acceptés :<br>
                    • <code>youtube.com/watch?v=...</code><br>
                    • <code>youtu.be/...</code><br>
                    • <code>youtube.com/embed/...</code><br>
                    • <code>youtube.com/shorts/...</code>
                </small>
            </div>

            <button type="submit" class="btn btn-primary">Ajouter la vidéo</button>
        </form>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
