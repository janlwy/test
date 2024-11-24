# Charte Graphique MediaBox

## 1. Palette de Couleurs

### Thème Clair
- Fond principal : #F5F5F5 (--clair)
- Texte : #182020 (--sombre)
- Accent primaire : #2f4f4f (--vertSombre)
- Accent secondaire : #000080 (--marine)
- Éléments interactifs : #747492 (--bleuArgent)

### Thème Sombre
- Fond principal : #464242 (--grisDark)
- Texte : #F5F5F5 (--clair)
- Accent primaire : #747492 (--bleuArgent)
- Accent secondaire : #ffb764 (--orange)
- Éléments interactifs : #bd2121 (--globale)

### Couleurs Communes
- Gris clair : #cccccc (--silver)
- Gris moyen : #808080 (--gris)
- Gris foncé : #696969 (--dimGray)
- Violet : #942494
- Gainsboro : #dcdcdcce

## 2. Typographie

### Police Principale
- Famille : 'Noto Sans Display', sans-serif
- Tailles :
  - H1 : 5rem
  - H2/H5 : 3rem
  - H3 : 1.5rem
  - Texte : 16px (medium)

### Icônes
- Système : Material Icons
- Tailles : 
  - md-48 : 48px
  - md-36 : 36px
  - md-24 : 24px
  - md-16 : 16px

## 3. Composants UI

### Boutons
- Classes : btnBase
- Variantes :
  - .vert : Actions positives (#008000)
  - .blue : Actions principales (#2b2bff)
  - .orange : Actions d'attention (#ffa500)
  - .rouge : Actions destructives (#ff0000)
  - .gris : Actions neutres (#808080)
  - .theme : Actions thématiques (variable selon le thème)

### Cartes
- Border-radius : 70px 6% (desktop)
- Ombre portée : variable selon le thème
- Transition sur hover

### Formulaires
- Inputs :
  - Border-radius : 13px
  - Padding : 12px 10px
  - Bordure : 1px solid #a8a4a4

### Messages
- Success : Fond vert, texte blanc
- Error : Fond rouge, texte blanc
- Border-radius : standard
- Durée d'affichage : 5 secondes

## 4. Responsive Design

### Breakpoints
- Mobile : < 600px
- Tablette : 601px - 999px
- Desktop : 1000px - 1999px
- Large Desktop : ≥ 2000px

### Adaptations Principales
- Navigation : Menu hamburger sur mobile
- Grille : Passage de 4 à 2 colonnes sur tablette
- Footer : Transformation en barre de navigation fixe sur mobile
- Taille des cartes : Ajustement selon l'écran

## 5. Animations

### Transitions
- Hover : opacity 0.2s
- Menu : max-height 0.2s ease-out
- Boutons : transform 0.3s ease-in-out

### Effets
- Cartes : Ombre portée dynamique
- Boutons : Scale sur hover
- Messages : Fade-in/Fade-out

## 6. Mise en Page

### Structure
- Largeur maximale : 85% (desktop)
- Marges : auto
- Espacement vertical : 20px entre sections

### Grille
- Display : flex
- Gap : 20px
- Colonnes : 
  - Desktop : 4 colonnes
  - Tablette : 2 colonnes
  - Mobile : 1 colonne

## 7. Accessibilité

### Contraste
- Texte/Fond : Ratio minimum 4.5:1
- Éléments interactifs : Indication visuelle claire

### Navigation
- Focus visible
- Alternatives textuelles pour les icônes
- Structure sémantique HTML
