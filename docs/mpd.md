# Modèle Physique de Données MediaBox

## Tables Existantes

### users
- id INT(10) PRIMARY KEY AUTO_INCREMENT
- pseudo TEXT NOT NULL
- email VARCHAR(255) NOT NULL
- mdp TEXT NOT NULL
- created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
- role VARCHAR(10) NOT NULL DEFAULT 'user'

### audio
- id INT(11) PRIMARY KEY AUTO_INCREMENT
- title VARCHAR(255) NOT NULL
- artist VARCHAR(255) NOT NULL
- image BLOB DEFAULT NULL
- path VARCHAR(255) NOT NULL
- user_id INT(11)
- FOREIGN KEY (user_id) REFERENCES users(id)

### photo
- id INT(11) PRIMARY KEY AUTO_INCREMENT
- title VARCHAR(255) NOT NULL
- description TEXT DEFAULT NULL
- path VARCHAR(255) NOT NULL
- user_id INT(11) NOT NULL
- created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

### text
- id INT(11) PRIMARY KEY AUTO_INCREMENT
- title VARCHAR(255) NOT NULL
- content TEXT NOT NULL
- user_id INT(11) NOT NULL
- created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

### video
- id INT(11) PRIMARY KEY AUTO_INCREMENT
- title VARCHAR(255) NOT NULL
- description TEXT DEFAULT NULL
- path VARCHAR(255) NOT NULL
- thumbnail VARCHAR(255) DEFAULT NULL
- user_id INT(11) NOT NULL
- created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

## Nouvelles Tables

### note_relations
- id INT(11) PRIMARY KEY AUTO_INCREMENT
- source_type ENUM('audio','video','photo','text') NOT NULL
- source_id INT(11) NOT NULL
- target_type ENUM('audio','video','photo','text') NOT NULL
- target_id INT(11) NOT NULL
- created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- user_id INT(11) NOT NULL
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
- UNIQUE KEY unique_relation (source_type, source_id, target_type, target_id, user_id)

### shared_notes
- id INT(11) PRIMARY KEY AUTO_INCREMENT
- note_type ENUM('audio','video','photo','text') NOT NULL
- note_id INT(11) NOT NULL
- shared_by INT(11) NOT NULL
- shared_with INT(11) NOT NULL
- created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- FOREIGN KEY (shared_by) REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY (shared_with) REFERENCES users(id) ON DELETE CASCADE
- UNIQUE KEY unique_share (note_type, note_id, shared_by, shared_with)

## Relations

### Relations Existantes
1. users -> audio (1:N)
2. users -> photo (1:N)
3. users -> text (1:N)
4. users -> video (1:N)

### Nouvelles Relations
5. note_relations -> users (N:1)
   - Chaque relation entre notes appartient à un utilisateur
   - La suppression d'un utilisateur supprime ses relations de notes

6. shared_notes -> users (N:2)
   - Chaque partage relie deux utilisateurs (partageur et destinataire)
   - La suppression d'un utilisateur supprime ses partages

## Contraintes

### note_relations
- Une relation doit avoir des types de notes valides
- Une relation doit référencer des notes existantes
- Un utilisateur ne peut créer qu'une seule relation entre les mêmes notes

### shared_notes
- Un partage doit avoir un type de note valide
- Un partage doit référencer une note existante
- Un utilisateur ne peut partager une note qu'une seule fois avec le même destinataire
