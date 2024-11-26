-- Création de la table des relations entre notes
CREATE TABLE note_relations (
    id INT(11) NOT NULL AUTO_INCREMENT,
    source_type ENUM('audio','video','photo','text') NOT NULL,
    source_id INT(11) NOT NULL,
    target_type ENUM('audio','video','photo','text') NOT NULL,
    target_id INT(11) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_id INT(11) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_relation (source_type, source_id, target_type, target_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Création de la table des partages de notes
CREATE TABLE shared_notes (
    id INT(11) NOT NULL AUTO_INCREMENT,
    note_type ENUM('audio','video','photo','text') NOT NULL,
    note_id INT(11) NOT NULL,
    shared_by INT(11) NOT NULL,
    shared_with INT(11) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (shared_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (shared_with) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_share (note_type, note_id, shared_by, shared_with)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
