<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

if (is_admin_logged_in()) {
    redirect('dashboard.php');
}

$error = '';
$lockedRemaining = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    if (!login_attempt_allowed()) {
        $lockedRemaining = login_lock_remaining();
        $error = 'Trop de tentatives. Réessayez dans ' . ceil($lockedRemaining / 60) . ' minute(s).';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $error = 'Identifiants incorrects.';
        } elseif (attempt_login($username, $password)) {
            redirect('dashboard.php');
        } else {
            if (!login_attempt_allowed()) {
                $lockedRemaining = login_lock_remaining();
                $error = 'Trop de tentatives. Réessayez dans ' . ceil($lockedRemaining / 60) . ' minute(s).';
            } else {
                $error = 'Identifiants incorrects.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Connexion Admin — <?= e(SITE_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-orange { background-color: #fd7e14; color: white; border: none; border-radius: 0.375rem; }
        .btn-orange:hover { background-color: #e56d0f; color: #fff; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="text-center mb-4">Connexion Administrateur</h4>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= e($error) ?></div>
                        <?php endif; ?>
                        <form method="POST" action="login.php" autocomplete="off">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <input type="text" name="username" class="form-control" placeholder="Nom d'utilisateur" required maxlength="100" autocomplete="username">
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Mot de passe" required autocomplete="current-password">
                            </div>
                            <button type="submit" class="btn btn-orange w-100">Se connecter</button>
                            <a href="../index.php" class="btn btn-secondary w-100 mt-2">Retour à l'accueil</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
