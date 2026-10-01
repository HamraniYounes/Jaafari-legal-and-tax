# Politique de sécurité

## ⚠️ Incident du dépôt public (à traiter en priorité)

Ce dépôt a historiquement contenu des secrets en clair. **Même après suppression d'un fichier,
l'historique git le conserve.** Procédure obligatoire :

1. **Changer immédiatement tous les secrets exposés** (ils doivent être considérés comme compromis) :
   - mot de passe de la base de données IONOS (`config/db.php` historique) ;
   - mot de passe de la boîte e-mail SMTP (`contact.php` historique).
2. **Nettoyer l'historique git** (sinon les secrets restent visibles dans les anciens commits) :
   ```bash
   # Avec git-filter-repo (recommandé) ou BFG Repo-Cleaner :
   git filter-repo --path config/db.php --invert-paths
   git filter-repo --path contact.php --invert-paths   # puis restaurer la version sécurisée
   # puis forcer le push :
   git push origin --force --all
   ```
   ⚠️ Le push forcé réécrit l'historique : coordonner avec les éventuels autres contributeurs.
3. Passer le dépôt en **privé** le temps du nettoyage si nécessaire.
4. Vérifier sur GitHub que l'onglet « Security → Secret scanning » ne signale plus de secret.

## Signaler une vulnérabilité

Ne pas ouvrir d'issue publique. Écrire directement au cabinet :
**walid@jaafari.be** (objet « Sécurité — [description courte] »). Réponse sous 48 h ouvrées.

## Mesures en place

Voir le tableau récapitulatif dans le [README](README.md#sécurité-résumé).

## Durcissement recommandé (évolution)

- [ ] CSP stricte avec nonces (décommenter dans `.htaccess` après tests).
- [ ] MFA (TOTP) pour le compte admin.
- [ ] HTTPS obligatoire côté hébergeur + renouvellement automatique du certificat.
- [ ] Sauvegardes chiffrées de la base de données.
- [ ] Surveillance : logs d'accès, alerte sur échecs de connexion répétés.
