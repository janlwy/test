# Modèle Conceptuel de Données - cda_projet

## Entités

### USERS
- id (PK)
- pseudo
- mdp
- role
- created_at

### AUDIO
- id (PK)
- title
- artist
- image
- path
- user_id (FK)
- created_at

### VIDEO
- id (PK)
- title
- description
- path
- thumbnail
- user_id (FK)
- created_at

### PHOTO
- id (PK)
- title
- description
- path
- user_id (FK)
- created_at

### TEXT
- id (PK)
- title
- content
- user_id (FK)
- created_at

## Relations

1. USERS (1,n) POSSÈDE AUDIO (1,1)
   - Un utilisateur peut avoir plusieurs fichiers audio
   - Un fichier audio appartient à un seul utilisateur

2. USERS (1,n) POSSÈDE VIDEO (1,1)
   - Un utilisateur peut avoir plusieurs vidéos
   - Une vidéo appartient à un seul utilisateur

3. USERS (1,n) POSSÈDE PHOTO (1,1)
   - Un utilisateur peut avoir plusieurs photos
   - Une photo appartient à un seul utilisateur

4. USERS (1,n) POSSÈDE TEXT (1,1)
   - Un utilisateur peut avoir plusieurs textes
   - Un texte appartient à un seul utilisateur

## Contraintes

- Tous les champs 'id' sont AUTO_INCREMENT
- Les champs 'created_at' utilisent TIMESTAMP avec valeur par défaut CURRENT_TIMESTAMP
- Les champs 'path' et 'image' stockent les chemins relatifs vers les fichiers
- Le champ 'role' dans USERS définit les permissions (ex: 'admin', 'user')
- Les mots de passe sont stockés de manière sécurisée (hachés)
