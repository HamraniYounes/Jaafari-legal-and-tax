# Guide de migration vers la version sécurisée

## 1. Sauvegarder
```bash
cp -r . ../jaafari-backup-$(date +%Y%m%d)
mysqldump -h DB_HOST -u DB_USER -p DB_NAME > backup.sql
```

## 2. Copier les nouveaux fichiers
Décompacter l'archive à la racine du site **en écrasant les anciens fichiers**.
Fichiers supprimés / renommés :
- `includes/Seo.php` → renommé en `includes/seo.php` (le nom avec majuscule cassait l'include sur les serveurs Linux sensibles à la casse). **Supprimer l'ancien `Seo.php`.**
- Rien d'autre n'est supprimé : `index.php`, `services.php`, `sitemap.php`, les traductions, `assets/`, `media/` restent tels quels.

## 3. Créer le `.env`
```bash
cp .env.example .env
# éditer .env avec les vraies valeurs
chmod 600 .env
```

## 4. Renouveler les secrets exposés
Comme indiqué dans [SECURITY.md](SECURITY.md) : changer le mot de passe DB et le mot de passe SMTP **avant** la mise en production.

## 5. Vérifier
- [ ] `https://jaafari.be` fonctionne (pages publiques).
- [ ] `https://jaafari.be/admin/login.php` fonctionne, les suppressions passent en POST.
- [ ] Le formulaire de contact envoie bien les e-mails.
- [ ] Accéder à `https://jaafari.be/config/db.php` renvoie une **erreur 403** (bloqué).
- [ ] Accéder à `https://jaafari.be/.env` renvoie une erreur 403/404.
- [ ] `git status` : `.env` n'apparaît pas (ignoré).

## 6. Nettoyer l'historique git
Suivre [SECURITY.md](SECURITY.md) (filter-repo/BFG + force push).
