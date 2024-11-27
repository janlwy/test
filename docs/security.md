# Documentation de Sécurité - MediaBox

## Introduction
La sécurité est un aspect fondamental de l'application MediaBox. Cette documentation détaille les mesures de sécurité implémentées pour protéger les données des utilisateurs et assurer l'intégrité du système.

## 1. Protection contre les Injections SQL

### Description
Les injections SQL représentent l'une des vulnérabilités les plus critiques dans les applications web. Elles permettent à un attaquant d'injecter du code SQL malveillant via les entrées utilisateur.

### Mesures implémentées
1. **Requêtes préparées PDO**
   - Utilisation systématique de `bindValue()` avec typage strict
   - Exemple :
     ```php
     $stmt = $connexion->prepare("SELECT * FROM users WHERE id = :id");
     $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
     ```

2. **Validation des entrées**
   - Vérification du type de données
   - Nettoyage des caractères spéciaux
   - Validation des identifiants de tables et colonnes

3. **Principe du moindre privilège**
   - Utilisation d'un compte base de données avec privilèges minimaux
   - Séparation des accès lecture/écriture

## 2. Protection XSS (Cross-Site Scripting)

### Description
Le XSS permet à un attaquant d'injecter du code JavaScript malveillant qui s'exécute dans le navigateur des utilisateurs.

### Mesures implémentées
1. **Échappement des sorties**
   - Utilisation systématique de `htmlspecialchars()`
   - Options de sécurité :
     ```php
     htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8')
     ```

2. **Headers de sécurité**
   - X-XSS-Protection: 1; mode=block
   - Content-Security-Policy (CSP)
   ```apache
   Content-Security-Policy: default-src 'self';
                          script-src 'self' 'unsafe-inline';
                          style-src 'self' 'unsafe-inline';
                          img-src 'self' data:;
   ```

3. **Validation des entrées**
   - Filtrage des caractères dangereux
   - Liste blanche des formats acceptés
   - Validation côté serveur ET client

## 3. Protection CSRF (Cross-Site Request Forgery)

### Description
Le CSRF force un utilisateur authentifié à exécuter des actions non désirées sur une application web.

### Implémentation détaillée
1. **Génération des tokens**
   ```php
   // Génération d'un token unique par session
   $token = bin2hex(random_bytes(32));
   $_SESSION['csrf_token'] = $token;
   $_SESSION['csrf_token_time'] = time();
   ```

2. **Validation**
   - Vérification du token sur chaque requête POST
   - Expiration après 1 heure
   - Double soumission des cookies

3. **Exemple d'utilisation**
   ```html
   <form method="POST">
     <input type="hidden" name="csrf_token" 
            value="<?php echo $_SESSION['csrf_token']; ?>">
   </form>
   ```

## 4. Politique de Mots de Passe

### Standards ANSI/NIST
1. **Exigences minimales**
   - Longueur : 8-72 caractères
   - Complexité :
     * Au moins 1 majuscule
     * Au moins 1 minuscule
     * Au moins 1 chiffre
     * Au moins 1 caractère spécial
   - Pas de mots du dictionnaire

2. **Validation**
   ```php
   $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,72}$/';
   if (!preg_match($pattern, $password)) {
       throw new ValidationException('Mot de passe invalide');
   }
   ```

3. **Stockage sécurisé**
   - Utilisation de bcrypt (coût = 12)
   - Salage unique par mot de passe
   - Rehashage périodique

## 5. Gestion des Sessions

### Configuration sécurisée
```php
session_set_cookie_params([
    'lifetime' => 1800,        // 30 minutes
    'path' => '/',
    'secure' => true,          // HTTPS uniquement
    'httponly' => true,        // Pas d'accès JavaScript
    'samesite' => 'Strict'     // Protection CSRF
]);
```

### Mesures additionnelles
1. **Régénération d'ID**
   - À chaque changement de privilège
   - Périodiquement (30 minutes)

2. **Expiration**
   - Timeout après inactivité
   - Déconnexion forcée après 24h

## 6. Conformité RGPD

### Principes implémentés
1. **Consentement**
   - Explicite et spécifique
   - Révocable à tout moment
   - Traçable et auditable

2. **Droits des utilisateurs**
   - Accès aux données
   - Rectification
   - Effacement (droit à l'oubli)
   - Portabilité

3. **Sécurité des données**
   - Chiffrement en transit (HTTPS)
   - Chiffrement au repos (AES-256)
   - Minimisation des données
   - Durée de conservation limitée

### Journalisation
```php
function logAccess($userId, $action, $data) {
    $log = [
        'timestamp' => time(),
        'user_id' => $userId,
        'action' => $action,
        'ip' => $_SERVER['REMOTE_ADDR'],
        'data' => $data
    ];
    // Stockage sécurisé des logs
}
```

## 7. Validation des Fichiers

### Upload sécurisé
1. **Vérifications**
   - Type MIME
   - Extension
   - Taille maximale
   - Scan antivirus (si disponible)

2. **Stockage**
   - Renommage aléatoire
   - Séparation du chemin public
   - Vérification des métadonnées

### Exemple de validation
```php
function validateFile($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'audio/mpeg'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        throw new SecurityException('Type de fichier non autorisé');
    }
    
    if ($file['size'] > $maxSize) {
        throw new SecurityException('Fichier trop volumineux');
    }
    
    // Vérifications supplémentaires...
}
```

## 8. Audit et Maintenance

### Procédures régulières
1. **Audit de sécurité**
   - Analyse statique du code
   - Tests de pénétration
   - Revue des logs

2. **Mises à jour**
   - Dépendances
   - Bibliothèques
   - Framework
   - Système d'exploitation

3. **Sauvegarde**
   - Chiffrée
   - Hors site
   - Testée régulièrement

## Conclusion

La sécurité est un processus continu qui nécessite une vigilance constante. Ces mesures sont régulièrement revues et mises à jour pour maintenir le plus haut niveau de protection possible pour nos utilisateurs.
