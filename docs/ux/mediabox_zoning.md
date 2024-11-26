# Zoning UX/UI - Vue Principale MediaBox

## Structure Générale

```ascii
+------------------------------------------+
|                HEADER                     |
|  [Logo/Titre]          [Bouton Thème]    |
+------------------------------------------+
|                  NAV                      |
| [Home][Profile][Audio][Video][Photo][Text]|
+------------------------------------------+
|              SEARCH BAR                   |
|    [Input Recherche]  [Bouton Search]    |
+------------------------------------------+
|              MAIN CONTENT                 |
|   +----------+           +----------+     |
|   |  AUDIO   |           |  VIDEO  |     |
|   |   Card   |           |   Card  |     |
|   +----------+           +----------+     |
|                                          |
|   +----------+           +----------+     |
|   |  PHOTO   |           |  TEXT   |     |
|   |   Card   |           |   Card  |     |
|   +----------+           +----------+     |
+------------------------------------------+
|                FOOTER                     |
|  [Home][Profile][Theme][Logout]          |
+------------------------------------------+

```

## Composants Détaillés

### 1. Header
- Position: Haut de page
- Contenu: 
  - Titre "MediaBox" (police Noto Sans Display)
  - Bouton de thème (clair/sombre) aligné à droite
- Style: Texte centré, ombre portée

### 2. Navigation
- Type: Barre horizontale
- Position: Sous le header
- Éléments:
  - Accueil (icône home)
  - Profil (nom utilisateur)
  - Audio
  - Vidéo
  - Photo
  - Texte
  - Déconnexion (aligné à droite)
- Style: Dégradé de couleurs, bordures arrondies

### 3. Barre de Recherche
- Position: Sous la navigation
- Composants:
  - Champ de recherche (placeholder "Trouver un artiste")
  - Bouton recherche avec icône
- Style: Design épuré, bordures arrondies

### 4. Grille de Cartes
- Layout: Grille 2x2 responsive
- Cartes:
  1. Audio (bleu)
     - Icône: headphones
     - Titre: "Audio"
  2. Vidéo (rouge)
     - Icône: live_tv
     - Titre: "Vidéo"
  3. Photo (vert)
     - Icône: photo_camera_back
     - Titre: "Photo"
  4. Texte (orange)
     - Icône: auto_stories
     - Titre: "Texte"
- Style: 
  - Bordures arrondies (70px 6%)
  - Ombre portée dynamique
  - Animation au survol

### 5. Footer
- Position: Bas de page
- Version Desktop:
  - Copyright
  - Liens sociaux
- Version Mobile:
  - Barre de navigation fixe
  - Icônes avec labels

## Responsive Design

### Desktop (>1000px)
- Grille: 4 cartes en ligne
- Footer: Standard avec copyright
- Navigation: Complète horizontale

### Tablette (601-999px)
- Grille: 2x2
- Menu: Hamburger
- Cartes: Taille adaptée

### Mobile (<600px)
- Grille: 1 colonne
- Menu: Hamburger
- Footer: Barre de navigation fixe
- Cartes: Pleine largeur

## Thèmes

### Clair
- Fond: #F5F5F5
- Texte: #182020
- Accents: #2f4f4f, #000080

### Sombre
- Fond: #464242
- Texte: #F5F5F5
- Accents: #747492, #ffb764
