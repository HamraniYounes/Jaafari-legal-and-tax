# JAAFARI Legal & Tax

Site vitrine du cabinet d'avocats **JAAFARI Legal & Tax** (Bruxelles) — PHP natif, Bootstrap 5, PHPMailer, base MySQL (IONOS).

**Prérequis : PHP ≥ 8.0** (chez IONOS : activer via le panneau d'administration → réglages PHP).

## Structure

```
├── index.php / services.php / actualites.php / contact.php / article.php / sitemap.php
├── admin/              # Back-office (login, dashboard, CRUD articles & vidéos)
├── assets/css/         # Feuilles de style
├── config/             # Configuration + connexion DB (.htaccess : accès web interdit)
├── includes/           # Bootstrap, auth, CSRF, fonctions, header/footer, traductions
│   └── translations/   # fr, en, nl, es, ar
├── media/              # Images, vidéos, PDF
└── PHPMailer/          # Bibliothèque d'envoi d'e-mails (.htaccess : accès web interdit)
```

## Installation

1. **Cloner** le dépôt sur le serveur.
2. **Créer le fichier `.env`** à la racine à partir de `.env.example` et y renseigner :
   - les identifiants de la base de données (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`) ;
   - les identifiants SMTP (`SMTP_USER`, `SMTP_PASS`, `MAIL_TO`, …).
   - ⚠️ `.env` est **ignoré par git** (`.gitignore`) : jamais commité.
3. S'assurer que les `.htaccess` sont actifs (Apache + `AllowOverride All`).
4. En développement local : mettre `APP_ENV=local` dans `.env` pour afficher les erreurs.

## Sécurité (résumé)

- Secrets dans `.env`, jamais dans le code ni dans git.
- Sessions : cookie `HttpOnly` + `SameSite=Lax` + `Secure` (HTTPS), régénération d'ID au login.
- Authentification admin : hachage `password_hash`/`password_verify`, anti brute-force (5 tentatives → 5 min de blocage), timeout d'inactivité (30 min).
- CSRF : jeton sur **tous** les formulaires POST (publics et admin), suppressions admin en POST uniquement.
- XSS : échappement systématique via `e()`, nettoyage du HTML des articles via `sanitize_html()` (anti `<script>`/`<iframe>`/`on*`).
- Requêtes SQL : **uniquement** des requêtes préparées (PDO, émulation désactivée).
- En-têtes de sécurité : HSTS, X-Frame-Options, nosniff, Referrer-Policy, Permissions-Policy (`.htaccess` + `includes/bootstrap.php`).
- Dossiers `config/`, `includes/`, `PHPMailer/` inaccessibles par le web (`.htaccess` `Require all denied`).

Voir [SECURITY.md](SECURITY.md) pour la politique de sécurité et la gestion d'incident.

## Comptes administrateurs

La table `admin` contient les comptes du back-office (`username`, `password` haché via `password_hash()`).
Pour créer un compte :

```php
echo password_hash('VotreMotDePasseFort', PASSWORD_DEFAULT);
-- puis insérer le hash dans la table `admin`
```

## Bonnes pratiques

- Ne jamais commité de secrets : `git status` doit toujours montrer `.env` comme ignoré.
- Compte rendu des erreurs dans les logs serveur, jamais à l'écran en production (`APP_ENV=production`).
