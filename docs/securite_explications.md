# Mesures de Sécurité de MediaBox - Guide Explicatif

## Introduction
Ce document explique en termes simples les différentes mesures de sécurité mises en place dans MediaBox, ainsi que les bonnes pratiques recommandées.

## 1. Protection des Mots de Passe
- Les mots de passe sont stockés de manière sécurisée (hachage avec bcrypt)
- Règles pour un mot de passe fort :
  * Au moins 8 caractères
  * Au moins une majuscule
  * Au moins une minuscule 
  * Au moins un chiffre
  * Au moins un caractère spécial (@$!%*#?&)
- Le mot de passe n'est jamais stocké en clair
- Limite de tentatives de connexion (3 essais puis blocage temporaire)

## 2. Protection Contre les Injections SQL
- Toutes les requêtes utilisent des requêtes préparées
- Les données utilisateur sont toujours validées avant utilisation
- Les erreurs de base de données ne révèlent pas d'informations sensibles
- Utilisation de comptes base de données avec privilèges minimaux

## 3. Protection Contre les Attaques XSS (Cross-Site Scripting)
- Toutes les données affichées sont échappées avec htmlspecialchars()
- Les balises HTML dangereuses sont filtrées
- En-têtes de sécurité configurés (Content-Security-Policy)
- Validation stricte des entrées utilisateur

## 4. Protection CSRF (Cross-Site Request Forgery)
- Jeton unique par session
- Validation du jeton sur chaque action sensible
- Double soumission des cookies
- Expiration des jetons après 1 heure

## 5. Gestion des Sessions
- ID de session régénéré régulièrement
- Expiration après 30 minutes d'inactivité
- Cookies sécurisés (httpOnly, secure, SameSite)
- Déconnexion automatique après 24h

## 6. Upload de Fichiers
- Vérification du type MIME
- Limite de taille (5MB images, 50MB audio)
- Renommage aléatoire des fichiers
- Scan antivirus si disponible
- Stockage hors de la racine web

## 7. Protection des Données (RGPD)
- Consentement explicite requis
- Droit à l'oubli (suppression des données)
- Accès aux données personnelles
- Chiffrement des données sensibles
- Durée de conservation limitée

## 8. Journalisation et Audit
- Journalisation des actions importantes
- Journalisation des erreurs
- Rotation des logs
- Pas d'informations sensibles dans les logs

## 9. Recommandations Utilisateurs
- Changer régulièrement son mot de passe
- Ne pas partager ses identifiants
- Se déconnecter après utilisation
- Utiliser un mot de passe unique
- Activer l'authentification à deux facteurs si disponible

## 10. Mesures Techniques Additionnelles
- HTTPS obligatoire
- En-têtes de sécurité HTTP
- Mise à jour régulière des dépendances
- Sauvegarde chiffrée des données
- Plan de reprise d'activité

## Conclusion
La sécurité est l'affaire de tous. Ces mesures sont régulièrement mises à jour pour garantir la meilleure protection possible des données des utilisateurs.

## Contact
Pour signaler un problème de sécurité : security@mediabox.com
