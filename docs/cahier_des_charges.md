# Cahier des Charges - MediaBox

## 1. Présentation du Projet

### 1.1 Contexte
MediaBox est une application web de gestion de médias personnels permettant aux utilisateurs de stocker, organiser et lire différents types de contenus multimédias.

### 1.2 Objectifs
- Centraliser le stockage des médias personnels
- Offrir une interface intuitive et responsive
- Assurer la sécurité des données utilisateurs
- Permettre la lecture des médias directement dans l'application

## 2. Spécifications Fonctionnelles

### 2.1 Gestion des Utilisateurs
- Inscription avec validation des données
- Connexion sécurisée
- Gestion des rôles (utilisateur/administrateur)
- Déconnexion avec nettoyage de session

### 2.2 Gestion des Médias
#### Audio
- Upload de fichiers audio (MP3, M4A, WAV, AAC, OGG)
- Lecture avec contrôles (play/pause, volume, progression)
- Gestion des métadonnées (titre, artiste)
- Affichage des pochettes
- Création de playlists

#### Vidéo
- Support des formats courants (MP4, WebM)
- Lecteur vidéo intégré
- Gestion des miniatures
- Contrôles de lecture personnalisés

#### Photos
- Upload d'images (JPEG, PNG, WebP)
- Organisation en albums
- Affichage optimisé
- Gestion des métadonnées

#### Texte
- Création et édition de notes
- Organisation par catégories
- Recherche dans le contenu

### 2.3 Interface Utilisateur
- Design responsive (Mobile, Tablette, Desktop)
- Thème clair/sombre
- Navigation intuitive
- Barre de recherche globale

## 3. Spécifications Techniques

### 3.1 Architecture
- Backend : PHP 8.2
- Base de données : MySQL 10.4
- Frontend : HTML5, CSS3, JavaScript
- Pattern MVC

### 3.2 Sécurité
- Protection contre les injections SQL
- Validation des entrées utilisateur
- Protection XSS
- Tokens CSRF
- Sessions sécurisées
- Hachage des mots de passe (bcrypt)

### 3.3 Base de Données
#### Tables Principales
- users (gestion des utilisateurs)
- audio (fichiers audio)
- video (fichiers vidéo)
- photo (images)
- text (notes textuelles)
- note_relations (liens entre médias)
- shared_notes (partage de contenus)

### 3.4 Performance
- Optimisation des requêtes
- Mise en cache
- Compression des assets
- Chargement différé des médias

## 4. Contraintes Techniques

### 4.1 Limitations
- Taille maximale des fichiers :
  * Audio : 50 MB
  * Images : 5 MB
  * Vidéos : 100 MB
- Formats de fichiers autorisés
- Résolutions d'écran supportées

### 4.2 Compatibilité
- Navigateurs modernes (Chrome, Firefox, Safari, Edge)
- Responsive design de 375px à 2000px+
- Support tactile pour mobiles/tablettes

## 5. Aspects Légaux et Conformité

### 5.1 RGPD
- Consentement explicite
- Droit à l'oubli
- Portabilité des données
- Protection des données personnelles

### 5.2 Accessibilité
- Standards WCAG 2.1
- Navigation au clavier
- Support des lecteurs d'écran
- Contraste et lisibilité

## 6. Maintenance et Évolution

### 6.1 Documentation
- Documentation technique
- Guide utilisateur
- Charte graphique
- Documentation de sécurité

### 6.2 Mises à jour
- Correctifs de sécurité
- Nouvelles fonctionnalités
- Optimisations performances
- Compatibilité navigateurs

## 7. Livrables

### 7.1 Application
- Code source commenté
- Base de données
- Assets médias
- Documentation

### 7.2 Documentation
- Manuel d'installation
- Guide d'utilisation
- Documentation technique
- Documentation de sécurité

## 8. Planning et Phases

### 8.1 Phase 1 - Core
- Authentification
- Gestion des utilisateurs
- Upload basique

### 8.2 Phase 2 - Médias
- Lecteur audio
- Lecteur vidéo
- Galerie photos
- Éditeur de texte

### 8.3 Phase 3 - Features
- Playlists
- Partage
- Relations entre médias
- Recherche avancée

### 8.4 Phase 4 - Polish
- Optimisations
- Tests
- Documentation
- Déploiement