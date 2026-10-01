<?php
// Pied de page du back-office (le <body> a été ouvert par header-admin.php).
?>
<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4 mb-4 mb-md-0">
                <h6 class="text-orange fw-bold mb-3">Plan du site</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="../index.php" class="text-white text-decoration-none">Accueil</a></li>
                    <li class="mb-2"><a href="../services.php" class="text-white text-decoration-none">Services</a></li>
                    <li class="mb-2"><a href="../actualites.php" class="text-white text-decoration-none">Actualités</a></li>
                    <li class="mb-2"><a href="../contact.php" class="text-white text-decoration-none">Contact</a></li>
                </ul>
            </div>

            <div class="col-md-4 mb-4 mb-md-0">
                <h6 class="text-orange fw-bold mb-3">Documents juridiques</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="../media/Convention%20client.pdf" target="_blank" rel="noopener" class="text-white text-decoration-none">Convention client</a></li>
                    <li class="mb-2"><a href="https://avocats.be/sites/avocatsbe/files/2025-04/04.04.2025-code-deontologie-version-francaise-en-vigueur-au-04.04.2025.pdf" target="_blank" rel="noopener" class="text-white text-decoration-none">Code de déontologie</a></li>
                </ul>
            </div>

            <div class="col-md-4">
                <h6 class="text-orange fw-bold mb-3">Informations</h6>
                <p class="small text-white mb-0">
                    Avenue des Sept Bonniers, 72<br>
                    1180 Bruxelles, Belgique<br>
                    BCE: 0781.811.189<br>
                    Email: <a href="mailto:walid@jaafari.be" class="text-white text-decoration-none">walid@jaafari.be</a>
                </p>
            </div>
        </div>

        <hr class="my-4 border-light opacity-50">

        <div class="text-center">
            &copy; <?= date('Y') ?> Jaafari Legal &amp; Tax. Tous droits réservés.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
