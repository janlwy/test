<?php
function shouldDisplayNav($datas): bool {
    // Ne pas afficher la navigation pour les pages d'administration
    if (isset($datas['isAdmin']) && $datas['isAdmin']) {
        return false;
    }
    
    // Afficher la navigation pour les utilisateurs connectés
    return isset($_SESSION['id']);
}
