<?php
// Pied de page — à inclure APRÈS le contenu d'une page (header.php a déjà ouvert <body>).
if (!isset($lang)) {
    $lang = [];
}
?>
<!-- Logo centré -->
<div style="text-align: center; padding-bottom: 1rem;">
    <img src="media/Logo-Walid-New.png" alt="Logo JAAFARI" style="width: 140px; height: auto;">
</div>

<!-- Footer -->
<footer class="text-white py-3" style="background-color: #7d2201; color: #ddd; padding: 40px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <!-- Colonne 1 : Plan du site -->
            <div class="col-md-4 mb-4 mb-md-0">
                <h6 class="text-light fw-bold mb-3" style="text-decoration: underline;"><?= e($lang['footer_plan'] ?? 'Plan du site') ?></h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="index.php" class="text-white text-decoration-none"><?= e($lang['nav_accueil'] ?? 'Accueil') ?></a></li>
                    <li class="mb-2"><a href="services.php" class="text-white text-decoration-none"><?= e($lang['nav_services'] ?? 'Services') ?></a></li>
                    <li class="mb-2"><a href="actualites.php" class="text-white text-decoration-none"><?= e($lang['nav_actualites'] ?? 'Actualités') ?></a></li>
                    <li class="mb-2"><a href="contact.php" class="text-white text-decoration-none"><?= e($lang['nav_contact'] ?? 'Contact') ?></a></li>
                </ul>
            </div>

            <!-- Colonne 2 : Documents juridiques -->
            <div class="col-md-4 mb-4 mb-md-0">
                <h6 class="text-light fw-bold mb-3" style="text-decoration: underline;"><?= e($lang['footer_legal'] ?? 'Documents juridiques') ?></h6>
                <ul class="list-unstyled small">
                    <li class="mb-2">
                        <a href="media/Convention%20client.pdf" target="_blank" rel="noopener" class="text-white text-decoration-none"><?= e($lang['footer_convention'] ?? 'Convention client') ?></a>
                    </li>
                    <li class="mb-2">
                        <a href="https://avocats.be/sites/avocatsbe/files/2025-04/04.04.2025-code-deontologie-version-francaise-en-vigueur-au-04.04.2025.pdf" target="_blank" rel="noopener" class="text-white text-decoration-none"><?= e($lang['footer_code'] ?? 'Code de déontologie') ?></a>
                    </li>
                </ul>
            </div>

            <!-- Colonne 3 : Informations + Réseaux sociaux -->
            <div class="col-md-4">
                <h6 class="text-light fw-bold mb-3" style="text-decoration: underline;"><?= e($lang['footer_info'] ?? 'Informations') ?></h6>
                <p class="small text-white mb-3">
                    Avenue des Sept Bonniers, 72<br>
                    1180 Bruxelles, Belgique<br>
                    +32 487 52 80 22<br>
                    BCE: 0781.811.189<br>
                    Email: <a href="mailto:walid@jaafari.be" class="text-white text-decoration-none">walid@jaafari.be</a>
                </p>

                <div class="d-flex gap-4 justify-content-start">
                    <a href="https://www.instagram.com/laminutefiscale_?igsh=d3lnb2NuMm42bGFp" target="_blank" rel="noopener" class="text-white text-decoration-none" aria-label="Instagram">
                        <i class="fab fa-instagram fs-5"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/walid-jaafari-a7bb8616a/" target="_blank" rel="noopener" class="text-white text-decoration-none" aria-label="LinkedIn">
                        <i class="fab fa-linkedin fs-5"></i>
                    </a>
                </div>
            </div>
        </div>

        <hr class="my-4 border-light opacity-50">

        <div class="text-center small">
            <?= $lang['footer_copyright'] ?? ('&copy; ' . date('Y') . ' Jaafari Legal &amp; Tax. Tous droits réservés.') ?>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
