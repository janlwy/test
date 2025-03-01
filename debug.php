<?php
// Fichier de débogage pour tester la connexion et la création de compte

// Afficher toutes les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclure les fichiers nécessaires
require_once __DIR__ . '/Fonctions/launch.php';
spl_autoload_register('charger');

// Tester la connexion à la base de données
try {
    $manager = new Models\Manager();
    $connexion = $manager->getConnexion();
    echo "<p style='color:green'>Connexion à la base de données réussie</p>";
    
    // Vérifier la table users
    $stmt = $connexion->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    echo "<p>Nombre d'utilisateurs dans la base de données: $count</p>";
    
    // Afficher les utilisateurs
    $stmt = $connexion->query("SELECT id, pseudo, email, role FROM users");
    echo "<h3>Liste des utilisateurs:</h3>";
    echo "<ul>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>ID: {$row['id']} - Pseudo: {$row['pseudo']} - Email: {$row['email']} - Rôle: {$row['role']}</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>Erreur de connexion à la base de données: " . $e->getMessage() . "</p>";
}

// Tester la session
echo "<h3>État de la session:</h3>";
echo "<pre>";
var_dump($_SESSION);
echo "</pre>";

// Tester la classe SessionManager
try {
    $session = Fonctions\SessionManager::getInstance();
    echo "<p style='color:green'>Instance de SessionManager créée avec succès</p>";
    
    // Vérifier si la méthode validateToken existe
    if (method_exists($session, 'validateToken')) {
        echo "<p>La méthode validateToken existe</p>";
    } else {
        echo "<p style='color:red'>La méthode validateToken n'existe pas</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>Erreur lors de la création de l'instance SessionManager: " . $e->getMessage() . "</p>";
}
?>

<h2>Formulaire de test de connexion</h2>
<form method="post" action="index.php?url=connexion/connect">
    <input type="text" name="pseudo" placeholder="Nom d'utilisateur" required><br>
    <input type="password" name="mdp" placeholder="Mot de passe" required><br>
    <input type="hidden" name="connexion" value="Connexion">
    <button type="submit">Se connecter</button>
</form>
