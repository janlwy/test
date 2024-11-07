<?php
require_once __DIR__ . '/../Models/Manager.php';
require_once __DIR__ . '/../Fonctions/SessionManager.php';
require_once __DIR__ . '/../Fonctions/RoleManager.php';

$session = SessionManager::getInstance();
$session->startSession();

// Vérification que l'utilisateur actuel est admin
if (!$session->get('role') === RoleManager::ROLE_ADMIN) {
    die('Accès non autorisé');
}

try {
    $manager = new Manager();
    $connexion = $manager->getConnexion();
    
    $sql = "UPDATE users SET role = :role WHERE pseudo = :pseudo";
    $req = $connexion->prepare($sql);
    $req->execute([
        ':role' => RoleManager::ROLE_ADMIN,
        ':pseudo' => 'admin'
    ]);
    
    if ($req->rowCount() > 0) {
        echo "Le rôle admin a été attribué avec succès à l'utilisateur 'admin'";
    } else {
        echo "Aucune modification n'a été effectuée. Vérifiez que l'utilisateur 'admin' existe.";
    }
} catch (Exception $e) {
    echo "Une erreur est survenue : " . htmlspecialchars($e->getMessage());
}
