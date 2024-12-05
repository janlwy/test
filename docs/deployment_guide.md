# Guide de Déploiement MediaBox

## 1. Prérequis Serveur

### Configuration Minimale
- PHP 8.0 ou supérieur
- MySQL 5.7 ou supérieur
- Extensions PHP requises :
  - PDO & PDO_MySQL
  - GD/ImageMagick
  - FileInfo
  - OpenSSL
  - Mbstring
  - JSON
  - Session
  - FFmpeg pour le traitement audio/vidéo

### Espace Disque
- Minimum 10GB pour l'application
- Espace supplémentaire pour les médias uploadés
- Prévoir une croissance de 20% par an

## 2. Sécurité

### Configuration PHP
```php
; php.ini
upload_max_filesize = 50M
post_max_size = 51M
memory_limit = 256M
max_execution_time = 300
session.cookie_secure = 1
session.cookie_httponly = 1
session.cookie_samesite = "Strict"
```

### Configuration MySQL
```sql
-- my.cnf
max_allowed_packet = 64M
innodb_buffer_pool_size = 256M
```

### Permissions Fichiers
```bash
# Répertoires d'upload
chmod 755 Ressources/
chmod 755 Ressources/audio/
chmod 755 Ressources/images/
chmod 755 Ressources/videos/
```

## 3. Étapes de Déploiement

### 1. Préparation
1. Sauvegarde de la base de données existante
2. Vérification des dépendances Composer
3. Test des migrations
4. Validation des fichiers de configuration

### 2. Déploiement
1. Transfert des fichiers
2. Configuration des variables d'environnement
3. Installation des dépendances
4. Exécution des migrations
5. Configuration du serveur web

### 3. Post-Déploiement
1. Vérification des logs
2. Test des fonctionnalités critiques
3. Monitoring des performances
4. Backup initial complet

## 4. Configuration Apache

### Virtual Host
```apache
<VirtualHost *:80>
    ServerName mediabox.example.com
    DocumentRoot /var/www/mediabox/public
    
    <Directory /var/www/mediabox/public>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/mediabox_error.log
    CustomLog ${APACHE_LOG_DIR}/mediabox_access.log combined
</Directory>
```

### .htaccess
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]

# Sécurité
Options -Indexes
ServerSignature Off
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
```

## 5. Monitoring

### Éléments à Surveiller
- Utilisation CPU/RAM
- Espace disque
- Temps de réponse
- Erreurs PHP/MySQL
- Taux de succès uploads
- Sessions actives

### Outils Recommandés
- NewRelic ou DataDog
- Logs système
- Monitoring MySQL
- Surveillance espace disque

## 6. Sauvegarde

### Stratégie
- Base de données : dump quotidien
- Fichiers médias : backup hebdomadaire
- Configuration : versionné avec git
- Rétention : 30 jours minimum

### Script de Backup
```bash
#!/bin/bash
# Backup DB
mysqldump -u user -p database > backup_$(date +%Y%m%d).sql

# Backup Media
tar -czf media_$(date +%Y%m%d).tar.gz Ressources/
```

## 7. Maintenance

### Tâches Régulières
- Nettoyage des fichiers temporaires
- Optimisation base de données
- Vérification des logs
- Mise à jour des dépendances
- Scan antivirus

### Crontab
```cron
0 2 * * * /path/to/backup.sh
0 3 * * * /usr/bin/php /path/to/cleanup.php
```

## 8. Scalabilité

### Considérations
- Séparation stockage médias (CDN)
- Cache Redis/Memcached
- Réplication MySQL
- Load balancing
- Containerisation possible

## 9. Environnements

### Production
- SSL obligatoire
- Logs détaillés
- Monitoring complet
- Backups automatiques

### Staging
- Mirror de production
- Données anonymisées
- Tests de performance
- Validation déploiements

## 10. Documentation

### À Maintenir
- Procédures de déploiement
- Configuration serveur
- Schéma base de données
- Procédures de backup/restore
- Contact support
