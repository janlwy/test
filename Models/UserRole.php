<?php
namespace Models;

class UserRole {
    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';
    
    private static $roleHierarchy = [
        self::ROLE_ADMIN => [self::ROLE_USER],
        self::ROLE_USER => []
    ];
    
    public static function hasRole(string $userRole, string $requiredRole): bool {
        if ($userRole === $requiredRole) {
            return true;
        }
        
        return in_array($requiredRole, self::$roleHierarchy[$userRole] ?? []);
    }
    
    public static function getAllowedRoutes(string $role): array {
        $routes = [
            self::ROLE_USER => [
                'mediabox', 'audio', 'video', 'photo', 'texte', 
                'compte', 'connexion', 'deconnexion'
            ],
            self::ROLE_ADMIN => [
                'admin', 'database_manager'
            ]
        ];
        
        $allowedRoutes = $routes[$role] ?? [];
        if (isset(self::$roleHierarchy[$role])) {
            foreach (self::$roleHierarchy[$role] as $inheritedRole) {
                $allowedRoutes = array_merge($allowedRoutes, $routes[$inheritedRole] ?? []);
            }
        }
        
        return array_unique($allowedRoutes);
    }
}
