<?php

trait RoleCheckerTrait {
    protected function requireRole(string $requiredRole) {
        $currentRole = isset($_SESSION['admin']) && $_SESSION['admin'] === true 
            ? UserRole::ROLE_ADMIN 
            : (isset($_SESSION['pseudo']) ? UserRole::ROLE_USER : '');
            
        if (!$currentRole || !UserRole::hasRole($currentRole, $requiredRole)) {
            logError("Tentative d'accès non autorisé - Rôle requis: $requiredRole, Rôle actuel: $currentRole");
            header('Location: ?url=connexion/index');
            exit();
        }
    }
}
