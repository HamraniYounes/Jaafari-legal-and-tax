<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/img.php';

// Inclure PHPMailer (bibliothèque vendorisée)
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    // Honeypot : champ invisible rempli uniquement par les bots
    if (!empty($_POST['website'])) {
        // Simuler un succès sans envoyer
        $message = '<div class="alert alert-success">Votre message a été envoyé avec succès.</div>';
    } else {
        // Nettoyer / valider les entrées
        $name         = trim(mb_substr((string) ($_POST['name'] ?? ''), 0, 100));
        $email        = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $message_body = trim(mb_substr((string) ($_POST['message'] ?? ''), 0, 5000));

        if ($name === '' || !$email || $message_body === '') {
            $message = '<div class="alert alert-danger">Veuillez remplir tous les champs correctement.</div>';
        } elseif (SMTP_USER === '' || SMTP_PASS === '' || MAIL_TO === '') {
            error_log('Contact : configuration SMTP manquante dans .env');
            $message = '<div class="alert alert-danger">Le service de messagerie est momentanément indisponible.</div>';
        } else {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = SMTP_USER;
                $mail->Password   = SMTP_PASS;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = SMTP_PORT;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
                $mail->addAddress(MAIL_TO, 'Me Walid Jaafari');
                $mail->addReplyTo($email, $name);

                $safeName    = e($name);
                $safeEmail   = e($email);
                $safeMessage = nl2br(e($message_body));

                $mail->isHTML(true);
                $mail->Subject = 'Message de contact - ' . $name;
                $mail->Body    = "
                <html>
                <body style='font-family: Arial, sans-serif; color: #333; line-height: 1.6;'>
                    <h2>Nouveau message de contact</h2>
                    <p><strong>Nom :</strong> {$safeName}</p>
                    <p><strong>Email :</strong> {$safeEmail}</p>
                    <p><strong>Message :</strong><br>{$safeMessage}</p>
                    <hr>
                    <p><em>Ce message a été envoyé depuis le site web JAAFARI Legal &amp; Tax.</em></p>
                </body>
                </html>";
                $mail->AltBody = "Nouveau message de contact\n\n" .
                    "Nom: {$name}\nEmail: {$email}\nMessage: {$message_body}\n\n" .
                    "Ce message a été envoyé depuis le site web JAAFARI Legal & Tax.";

                $mail->send();
                $message = '<div class="alert alert-success">Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.</div>';
                $_POST = [];
            } catch (Exception $e) {
                // Ne JAMAIS afficher l'erreur technique au visiteur
                error_log('Erreur envoi mail contact : ' . $mail->ErrorInfo);
                $message = '<div class="alert alert-danger">Échec de l\'envoi du message. Veuillez réessayer plus tard.</div>';
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
    body { font-family: 'Lato', sans-serif; background-color: #fdfaf6; color: #333; }
    .section-title { font-size: 2.5rem; color: #da6600; position: relative; display: inline-block; margin-bottom: 1.5rem; }
    .section-title::after { content: ''; position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 60px; height: 3px; background: #f97316; }
    .walid-contact { max-width: 380px; border: 3px solid #f8f1e9; border-radius: 16px; object-fit: cover; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); transition: all 0.3s ease; }
    .walid-contact:hover { transform: translateY(-4px); box-shadow: 15px 10px 30px rgba(0, 0, 0, 0.25); }
    .contact-card { background-color: white; border-radius: 12px; padding: 20px; box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08); transition: transform 0.3s ease; }
    .contact-card:hover { transform: translateY(-4px); }
    .contact-info-line { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 1rem; font-size: 1rem; line-height: 1.6; }
    .contact-info-line i { color: #da6600; min-width: 24px; margin-top: 4px; }
    .btn-orange { background-color: #da6600 !important; border-color: #da6600 !important; color: white !important; padding: 12px 28px; font-weight: 600; border-radius: 50px; font-size: 1rem; transition: all 0.3s ease; box-shadow: 0 4px 10px rgba(218, 102, 0, 0.2); }
    .btn-orange:hover { background-color: #b85600 !important; border-color: #b85600 !important; transform: scale(1.05); box-shadow: 0 6px 15px rgba(218, 102, 0, 0.3); }
    .form-control { border: 1px solid #ddd; border-radius: 8px; padding: 10px 14px; font-size: 1rem; }
    .form-control:focus { border-color: #da6600; box-shadow: 0 0 0 0.2rem rgba(218, 102, 0, 0.15); }
    .text-justify { text-align: justify; line-height: 1.7; font-size: 1.05rem; }
    .photo-text-section { margin-top: 6rem; text-align: center; }
    .photo-text-section h3 { margin-bottom: 1.5rem; color: #da6600; }
    .texte-infos { font-size: 1.4rem; }
    /* Honeypot : invisible pour les humains */
    .hp-field { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }

    @media (min-width: 768px) {
        .photo-text-section { text-align: left; }
        .photo-text-content { display: flex; align-items: center; gap: 40px; }
        .walid-contact { margin: 0; }
    }
    @media (max-width: 768px) {
        .section-title { font-size: 2.2rem; }
        .photo-text-content { flex-direction: column; text-align: center; }
        .walid-contact { margin-bottom: 2rem; }
        .texte-infos { font-size: 0.7rem; }
    }
</style>

<div class="container py-5">
    <!-- Message d'alerte -->
    <?php if ($message !== ''): ?>
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <?= $message ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Section : Formulaire + Coordonnées -->
    <div class="row mb-5">
        <!-- Coordonnées -->
        <div class="col-lg-5 mb-4 mb-lg-0">
            <h1 class="section-title" style="color: #da6600;"><?= e($lang['contact_work']) ?></h1><br>
            <div class="contact-card">
                <div class="contact-info-line">
                    <i class="fa-solid fa-location-pin"></i>
                    <span><strong><?= e($lang['contact_address']) ?></strong><br> Avenue des Sept Bonniers, 72, 1180 Bruxelles, Belgique</span>
                </div>
                <div class="contact-info-line">
                    <i class="fa-solid fa-phone"></i>
                    <span><strong><?= e($lang['contact_phone']) ?></strong><br>+32 487 52 80 22</span>
                </div>
                <div class="contact-info-line">
                    <i class="fa-solid fa-envelope"></i>
                    <span><strong><?= e($lang['contact_email']) ?></strong><br>walid@jaafari.be</span>
                </div>
                <div class="contact-info-line">
                    <i class="fa-solid fa-calendar"></i>
                    <span><strong><?= e($lang['contact_hours']) ?></strong><br><?= e($lang['contact_mon_fri']) ?><br><?= e($lang['contact_saturday']) ?></span>
                </div>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="col-lg-7">
            <h2 class="section-title" style="color: #da6600;"><?= e($lang['contact_title']) ?></h2>
            <form method="POST" action="contact.php" novalidate>
                <?= csrf_field() ?>
                <!-- Honeypot anti-bot : ne pas remplir -->
                <div class="hp-field" aria-hidden="true">
                    <label>Ne pas remplir ce champ<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>
                <div class="mb-3">
                    <input type="text" name="name" class="form-control" placeholder="<?= e($lang['form_name']) ?>"
                           value="<?= e($_POST['name'] ?? '') ?>" required maxlength="100">
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="<?= e($lang['form_email']) ?>"
                           value="<?= e($_POST['email'] ?? '') ?>" required maxlength="190">
                </div>
                <div class="mb-3">
                    <textarea name="message" class="form-control" rows="5" placeholder="<?= e($lang['form_message']) ?>" required maxlength="5000"><?= e($_POST['message'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-orange"><?= e($lang['btn_send']) ?></button>
            </form>
        </div>
    </div>

    <!-- Section : Photo + Texte -->
    <div class="photo-text-section">
        <div class="photo-text-content">
            <div class="col-lg-4 d-flex justify-content-center">
                <img src="media/walid-new.jpg" alt="Me Walid Jaafari - Avocat Fiscaliste Bruxelles"
                     class="img-fluid walid-contact rounded-4" <?= img_attrs('media/walid-new.jpg') ?> loading="lazy" decoding="async">
            </div>
            <div class="flex-grow-1">
                <p class="text-justify texte-infos">
                    <?= $lang['contact_info_text'] ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
