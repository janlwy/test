# Dictionnaire de Données MediaBox

## Table: users
| Champ      | Type         | Taille | Description                    | Contraintes |
|------------|--------------|---------|--------------------------------|-------------|
| id         | INT          | -       | Identifiant unique utilisateur | PK, AUTO_INCREMENT |
| pseudo     | VARCHAR      | 50      | Nom d'utilisateur             | UNIQUE, NOT NULL |
| email      | VARCHAR      | 100     | Adresse email                 | UNIQUE, NOT NULL |
| mdp        | VARCHAR      | 255     | Mot de passe hashé            | NOT NULL |
| role       | VARCHAR      | 20      | Rôle utilisateur (user/admin) | NOT NULL, DEFAULT 'user' |
| created_at | TIMESTAMP    | -       | Date de création              | DEFAULT CURRENT_TIMESTAMP |

## Table: audio
| Champ      | Type         | Taille | Description                    | Contraintes |
|------------|--------------|---------|--------------------------------|-------------|
| id         | INT          | -       | Identifiant unique audio      | PK, AUTO_INCREMENT |
| title      | VARCHAR      | 100     | Titre de la piste audio       | NOT NULL |
| artist     | VARCHAR      | 100     | Nom de l'artiste              | NOT NULL |
| image      | VARCHAR      | 255     | Chemin de la pochette         | NOT NULL |
| path       | VARCHAR      | 255     | Chemin du fichier audio       | NOT NULL |
| user_id    | INT          | -       | ID de l'utilisateur           | FK (users.id) |
| created_at | TIMESTAMP    | -       | Date d'ajout                  | DEFAULT CURRENT_TIMESTAMP |

## Table: video (à implémenter)
| Champ      | Type         | Taille | Description                    | Contraintes |
|------------|--------------|---------|--------------------------------|-------------|
| id         | INT          | -       | Identifiant unique vidéo      | PK, AUTO_INCREMENT |
| title      | VARCHAR      | 100     | Titre de la vidéo             | NOT NULL |
| description| TEXT         | -       | Description de la vidéo       | NULL |
| thumbnail  | VARCHAR      | 255     | Chemin de la miniature        | NOT NULL |
| path       | VARCHAR      | 255     | Chemin du fichier vidéo       | NOT NULL |
| user_id    | INT          | -       | ID de l'utilisateur           | FK (users.id) |
| created_at | TIMESTAMP    | -       | Date d'ajout                  | DEFAULT CURRENT_TIMESTAMP |

## Table: photo (à implémenter)
| Champ      | Type         | Taille | Description                    | Contraintes |
|------------|--------------|---------|--------------------------------|-------------|
| id         | INT          | -       | Identifiant unique photo      | PK, AUTO_INCREMENT |
| title      | VARCHAR      | 100     | Titre de la photo             | NOT NULL |
| description| TEXT         | -       | Description de la photo       | NULL |
| path       | VARCHAR      | 255     | Chemin du fichier photo       | NOT NULL |
| user_id    | INT          | -       | ID de l'utilisateur           | FK (users.id) |
| created_at | TIMESTAMP    | -       | Date d'ajout                  | DEFAULT CURRENT_TIMESTAMP |

## Table: texte (à implémenter)
| Champ      | Type         | Taille | Description                    | Contraintes |
|------------|--------------|---------|--------------------------------|-------------|
| id         | INT          | -       | Identifiant unique texte      | PK, AUTO_INCREMENT |
| title      | VARCHAR      | 100     | Titre du texte                | NOT NULL |
| content    | TEXT         | -       | Contenu du texte              | NOT NULL |
| user_id    | INT          | -       | ID de l'utilisateur           | FK (users.id) |
| created_at | TIMESTAMP    | -       | Date d'ajout                  | DEFAULT CURRENT_TIMESTAMP |

## Notes sur les contraintes:
- Toutes les tables ont une clé primaire auto-incrémentée (id)
- Les champs created_at utilisent CURRENT_TIMESTAMP par défaut
- Les champs user_id sont des clés étrangères vers la table users
- Les chemins de fichiers (path, image, thumbnail) sont stockés en relatif
- Les suppressions en cascade sont activées sur les clés étrangères
