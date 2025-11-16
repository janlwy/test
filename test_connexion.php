<?php
require_once 'config.php';
require_once 'Models/Bdd.php';
require_once 'Models/DatabaseException.php';

use Models\Bdd;
use Models\DatabaseException;

// Fonction logError temporaire si elle n'existe pas
if (!function_exists('logError')) {
    function logError($message) {
        error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, 'error.log');
        echo "ERREUR: " . $message . "\n";
    }
}

echo "=== Test de connexion à la base de données ===\n\n";

// Vérifier si les constantes sont définies
echo "1. Vérification des constantes de configuration:\n";
$constantes = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD'];
$constantesManquantes = [];

foreach ($constantes as $constante) {
    if (defined($constante)) {
        echo "   ✓ $constante est définie\n";
    } else {
        echo "   ✗ $constante n'est PAS définie\n";
        $constantesManquantes[] = $constante;
    }
}

if (!empty($constantesManquantes)) {
    echo "\nERREUR: Les constantes suivantes doivent être définies:\n";
    foreach ($constantesManquantes as $constante) {
        echo "   - $constante\n";
    }
    echo "\nVeuillez créer un fichier de configuration avec ces constantes.\n";
    exit(1);
}

echo "\n2. Test de connexion à la base de données:\n";

try {
    $connexion = Bdd::getConnexion();
    echo "   ✓ Connexion réussie !\n";
    
    // Test d'une requête simple
    echo "\n3. Test d'une requête simple:\n";
    $stmt = $connexion->query("SELECT 1 as test");
    $result = $stmt->fetch();
    
    if ($result && $result['test'] == 1) {
        echo "   ✓ Requête de test réussie !\n";
    } else {
        echo "   ✗ Problème avec la requête de test\n";
    }
    
    // Afficher des informations sur la base de données
    echo "\n4. Informations sur la base de données:\n";
    $stmt = $connexion->query("SELECT DATABASE() as db_name, VERSION() as version");
    $info = $stmt->fetch();
    echo "   Base de données: " . $info['db_name'] . "\n";
    echo "   Version MySQL: " . $info['version'] . "\n";
    
    echo "\n✅ SUCCÈS: Votre application est bien connectée à la base de données !\n";
    
} catch (DatabaseException $e) {
    echo "   ✗ Erreur de connexion: " . $e->getMessage() . "\n";
    
    if ($e->getPrevious()) {
        echo "   Détails: " . $e->getPrevious()->getMessage() . "\n";
    }
    
    echo "\n❌ ÉCHEC: Problème de connexion à la base de données\n";
    
    // Suggestions de dépannage
    echo "\nSuggestions de dépannage:\n";
    echo "1. Vérifiez que MySQL/MariaDB est démarré\n";
    echo "2. Vérifiez les paramètres de connexion (host, nom de base, utilisateur, mot de passe)\n";
    echo "3. Vérifiez que l'utilisateur a les permissions sur la base de données\n";
    echo "4. Vérifiez que la base de données existe\n";
    
} catch (Exception $e) {
    echo "   ✗ Erreur inattendue: " . $e->getMessage() . "\n";
    echo "\n❌ ÉCHEC: Erreur inattendue\n";
}

echo "\n=== Fin du test ===\n";
?>
