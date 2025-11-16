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

if (!function_exists('logInfo')) {
    function logInfo($message) {
        error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, 'info.log');
        echo "INFO: " . $message . "\n";
    }
}

echo "=== Création d'un compte utilisateur ===\n\n";

// Données de l'utilisateur de test
$userData = [
    'pseudo' => 'testuser',
    'email' => 'test@example.com',
    'password' => 'TestPassword123!',
    'role' => 'user'
];

try {
    $connexion = Bdd::getConnexion();
    echo "✓ Connexion à la base de données réussie\n";
    
    // Vérifier si l'utilisateur existe déjà
    $checkSql = "SELECT COUNT(*) FROM users WHERE pseudo = :pseudo OR email = :email";
    $checkStmt = $connexion->prepare($checkSql);
    $checkStmt->bindValue(':pseudo', $userData['pseudo'], PDO::PARAM_STR);
    $checkStmt->bindValue(':email', $userData['email'], PDO::PARAM_STR);
    $checkStmt->execute();
    
    $existingCount = $checkStmt->fetchColumn();
    
    if ($existingCount > 0) {
        echo "⚠ L'utilisateur '{$userData['pseudo']}' ou l'email '{$userData['email']}' existe déjà\n";
        
        // Proposer de mettre à jour le mot de passe
        echo "Voulez-vous mettre à jour le mot de passe ? (y/N): ";
        $handle = fopen("php://stdin", "r");
        $response = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($response) === 'y' || strtolower($response) === 'yes') {
            $updateSql = "UPDATE users SET mdp = :password, updated_at = NOW() WHERE pseudo = :pseudo";
            $updateStmt = $connexion->prepare($updateSql);
            $updateStmt->bindValue(':password', password_hash($userData['password'], PASSWORD_DEFAULT), PDO::PARAM_STR);
            $updateStmt->bindValue(':pseudo', $userData['pseudo'], PDO::PARAM_STR);
            
            if ($updateStmt->execute()) {
                echo "✓ Mot de passe mis à jour avec succès\n";
            } else {
                throw new Exception("Erreur lors de la mise à jour du mot de passe");
            }
        } else {
            echo "Opération annulée\n";
        }
    } else {
        // Créer le nouvel utilisateur
        $insertSql = "INSERT INTO users (pseudo, email, mdp, role, created_at) VALUES (:pseudo, :email, :password, :role, NOW())";
        $insertStmt = $connexion->prepare($insertSql);
        $insertStmt->bindValue(':pseudo', $userData['pseudo'], PDO::PARAM_STR);
        $insertStmt->bindValue(':email', $userData['email'], PDO::PARAM_STR);
        $insertStmt->bindValue(':password', password_hash($userData['password'], PASSWORD_DEFAULT), PDO::PARAM_STR);
        $insertStmt->bindValue(':role', $userData['role'], PDO::PARAM_STR);
        
        if ($insertStmt->execute()) {
            $userId = $connexion->lastInsertId();
            echo "✓ Utilisateur créé avec succès (ID: $userId)\n";
        } else {
            throw new Exception("Erreur lors de la création de l'utilisateur");
        }
    }
    
    echo "\n=== Informations de connexion ===\n";
    echo "Nom d'utilisateur: {$userData['pseudo']}\n";
    echo "Email: {$userData['email']}\n";
    echo "Mot de passe: {$userData['password']}\n";
    echo "Rôle: {$userData['role']}\n";
    echo "\n✅ Vous pouvez maintenant vous connecter à l'application avec ces identifiants.\n";
    
} catch (DatabaseException $e) {
    echo "✗ Erreur de base de données: " . $e->getMessage() . "\n";
    
    if ($e->getPrevious()) {
        echo "Détails: " . $e->getPrevious()->getMessage() . "\n";
    }
    
    echo "\n❌ ÉCHEC: Impossible de créer l'utilisateur\n";
    
} catch (Exception $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n";
    echo "\n❌ ÉCHEC: Erreur lors de la création de l'utilisateur\n";
}

echo "\n=== Fin de la création d'utilisateur ===\n";
?>
