<?php
namespace Fonctions;

class RoleManager {
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';
    public const ROLE_GUEST = 'guest';
    
    private static $VALID_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_USER,
        self::ROLE_GUEST
    ];
    
    public static function isValidRole(string $role): bool {
        return in_array($role, self::$VALID_ROLES);
    }
    
    public static function getDefaultRole(): string {
        return self::ROLE_USER;
    }
    
    public static function hasPermission(string $userRole, string $requiredRole): bool {
        $roleHierarchy = [
            self::ROLE_ADMIN => 3,
            self::ROLE_USER => 2,
            self::ROLE_GUEST => 1
        ];
        
        return ($roleHierarchy[$userRole] ?? 0) >= ($roleHierarchy[$requiredRole] ?? 0);
    }
}
