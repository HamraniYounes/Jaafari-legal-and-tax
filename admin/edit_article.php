<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_admin();

$error = $success = '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    exit('Identifiant invalide.');
}

$stmt = $pdo->prepare('SELECT * FROM articles WHERE id = ?');
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    http_response_code(404);
    exit('Article non trouvé.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $title   = trim(mb_substr((string) ($_POST['title'] ?? ''), 0, 255));
    $content = trim((string) ($_POST['content'] ?? ''));
    $author  = trim(mb_substr((string) ($_POST['author'] ?? ''), 0, 100)) ?: 'Me Walid Jaafari';
    $publish_date = date('Y-m-d');

    if ($title === '' || $content === '') {
        $error = 'Le titre et le contenu sont obligatoires.';
    } else {
        $stmt = $pdo->prepare('UPDATE articles SET title = ?, content = ?, author = ?, publish_date = ? WHERE id = ?');
        if ($stmt->execute([$title, $content, $author, $publish_date, $id])) {
            $success = 'Article mis à jour avec succès.';
        } else {
            $error = 'Erreur lors de la mise à jour.';
        }
    }
}

include __DIR__ . '/../includes/header-admin.php';
?>

<div class="container py-5">
    <h2 class="section-title">Modifier l'Article</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
        <a href="dashboard.php" class="btn btn-orange">Retour au tableau de bord</a>
    <?php else: ?>
        <form method="POST" action="edit_article.php?id=<?= (int) $id ?>" onsubmit="return syncEditorContent();">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Titre</label>
                <input type="text" name="title" class="form-control" value="<?= e($article['title']) ?>" required maxlength="255">
            </div>
            <div class="mb-3">
                <label class="form-label">Auteur</label>
                <input type="text" name="author" class="form-control" value="<?= e($article['author']) ?>" maxlength="100">
            </div>

            <div class="mb-3">
                <label class="form-label">Contenu</label>
                <div id="editor" style="height: 300px;"><?= sanitize_html($article['content']) ?></div>
                <textarea name="content" id="hiddenContent" style="display:none;"></textarea>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Mettre à jour</button>
        </form>
    <?php endif; ?>
</div>

<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<script>
var quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            ['link', 'image'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['blockquote', 'code-block'],
            ['clean']
        ]
    }
});

function syncEditorContent() {
    document.getElementById('hiddenContent').value = quill.root.innerHTML;
    return true;
}
</script>

<style>
#editor { background-color: #fff; }
</style>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
