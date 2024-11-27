# Sécurité de l'Application MediaBox

## 1. Protection contre les Injections SQL

L'application utilise systématiquement des requêtes préparées PDO avec des paramètres liés pour toutes les interactions avec la base de données. Exemples :
- Utilisation de `bindValue()` avec typage strict
- Échappement automatique des caractères spéciaux
- Validation des identifiants de tables et colonnes

## 2. Protection XSS (Cross-Site Scripting)

Toutes les sorties sont échappées via :
- `htmlspecialchars()` avec ENT_QUOTES pour les données affichées
- Validation stricte des entrées utilisateur
- Headers de sécurité appropriés (X-XSS-Protection)
- Content Security Policy (CSP) implémentée

## 3. Protection CSRF (Cross-Site Request Forgery)

Mécanismes mis en place :
- Tokens CSRF uniques par session
- Validation du token sur toutes les requêtes POST
- Vérification de l'origine des requêtes
- Expiration des tokens après 1 heure

## 4. Politique de Mots de Passe (ANSI)

Exigences minimales :
- 8 caractères minimum
- Au moins 1 majuscule
- Au moins 1 minuscule  
- Au moins 1 chiffre
- Au moins 1 caractère spécial
- Maximum 72 caractères (limite bcrypt)
- Pas de mots courants/dictionnaire

## 5. Hashage des Mots de Passe

Implémentation :
- Utilisation de bcrypt via password_hash()
- Coût de hashage adaptatif
- Salage unique par mot de passe
- Pas de stockage en clair
- Rehashage périodique recommandé

## 6. Conformité RGPD

Mesures implémentées :
- Consentement explicite requis
- Droit à l'oubli (suppression de compte)
- Portabilité des données
- Minimisation des données collectées
- Chiffrement des données sensibles
- Journal des accès aux données
- Politique de confidentialité claire

## 7. Autres Mesures de Sécurité

- Sessions sécurisées (httpOnly, secure)
- Délai d'expiration des sessions
- Validation des uploads de fichiers
- Logs de sécurité détaillés
- Mises à jour régulières des dépendances
- Audit de sécurité périodique
