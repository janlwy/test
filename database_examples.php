<?php
require_once 'Models/Manager.php';

// Création d'une instance du gestionnaire de base de données
$manager = new Manager();

try {
    // 1. Exemple de création d'une nouvelle table
    $manager->createTable('utilisateurs', [
        'nom' => 'VARCHAR(50) NOT NULL',
        'email' => 'VARCHAR(100) NOT NULL UNIQUE',
        'age' => 'INT',
        'date_inscription' => 'DATETIME DEFAULT CURRENT_TIMESTAMP'
    ]);
    echo "Table 'utilisateurs' créée avec succès\n";

    // 2. Exemple d'ajout d'une nouvelle colonne
    $manager->addColumn('utilisateurs', 'telephone', 'VARCHAR(15)');
    echo "Colonne 'telephone' ajoutée avec succès\n";

    // 3. Exemple de modification d'une colonne existante
    $manager->modifyColumn('utilisateurs', 'telephone', 'VARCHAR(20) NOT NULL DEFAULT "non renseigné"');
    echo "Colonne 'telephone' modifiée avec succès\n";

} catch (DatabaseException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?>
