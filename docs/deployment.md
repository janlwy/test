# Guide de Déploiement MediaBox

## 1. Environnements

### Développement (Local)
- Base de données : MySQL local
- Serveur web : Apache/PHP local
- Configuration debug activée
- Variables d'environnement dev dans config.php

### Recette/Test
- Base de données : MySQL dédié
- Serveur web : Apache/PHP mutualisé
- Tests automatisés activés
- Variables d'environnement test

### Production
- Base de données : MySQL dédié haute disponibilité
- Serveur web : Apache/PHP dédié
- Logs et monitoring activés
- Variables d'environnement prod

## 2. Procédure de Déploiement

### 2.1 Préparation
1. Vérification des tests
```bash
phpunit tests/
```

2. Validation du code
```bash
php -l **/*.php
composer validate
```

3. Sauvegarde de la base de données
```sql
mysqldump -u root -p cda_projet > backup_$(date +%Y%m%d).sql
```

### 2.2 Déploiement
1. Transfert des fichiers
```bash
rsync -avz --exclude '.git' --exclude 'tests' --exclude 'docs' ./ user@production:/var/www/mediabox/
```

2. Mise à jour de la base de données
```sql
mysql -u prod_user -p prod_db < Database/updates/*.sql
```

3. Configuration
- Mise à jour config.php avec les paramètres de production
- Ajustement des permissions des dossiers
```bash
chmod 755 /var/www/mediabox
chmod -R 644 /var/www/mediabox/Ressources
```

### 2.3 Vérifications Post-Déploiement
1. Tests fonctionnels
- Connexion utilisateur
- Upload de médias
- Lecture des médias
- Gestion des droits

2. Monitoring
- Vérification des logs Apache/PHP
- Surveillance des performances
- Contrôle des accès

## 3. Rollback

### 3.1 Procédure de Retour Arrière
1. Restauration des fichiers
```bash
rsync -avz backup/ /var/www/mediabox/
```

2. Restauration de la base
```sql
mysql -u prod_user -p prod_db < backup_YYYYMMDD.sql
```

## 4. Maintenance

### 4.1 Sauvegarde
- Base de données : quotidienne
- Fichiers médias : hebdomadaire
- Rétention : 30 jours

### 4.2 Monitoring
- Surveillance CPU/RAM/Disque
- Logs d'erreurs PHP/MySQL
- Temps de réponse
- Taux d'erreurs

### 4.3 Sécurité
- Mises à jour de sécurité
- Scan antivirus
- Audit des accès
- Vérification des permissions

## 5. Documentation

### 5.1 Pour les Administrateurs
- Procédures de déploiement
- Gestion des incidents
- Maintenance quotidienne
- Contacts d'urgence

### 5.2 Pour les Utilisateurs
- Guide d'utilisation
- FAQ
- Procédures de support
- Signalement des problèmes

## 6. Contacts

### Support Technique
- Email : support@mediabox.com
- Téléphone : +XX XX XX XX XX
- Horaires : 9h-18h

### Urgences (24/7)
- Email : urgence@mediabox.com
- Téléphone : +XX XX XX XX XX
# Guide de Déploiement MediaBox

## 1. Prérequis

### Environnement de Production
- PHP 8.2 ou supérieur
- MySQL 8.0 ou supérieur
- Apache 2.4 ou supérieur
- Extensions PHP requises:
  - PDO
  - PDO_MySQL
  - GD
  - FileInfo
  - OpenSSL

### Configuration Apache
```apache
<VirtualHost *:80>
    ServerName mediabox.example.com
    DocumentRoot /var/www/mediabox
    
    <Directory /var/www/mediabox>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/mediabox_error.log
    CustomLog ${APACHE_LOG_DIR}/mediabox_access.log combined
</VirtualHost>
```

## 2. Étapes de Déploiement

### 2.1 Préparation
1. Sauvegarder la base de données existante
```bash
mysqldump -u root -p cda_projet > backup_$(date +%Y%m%d).sql
```

2. Créer les dossiers nécessaires
```bash
mkdir -p /var/www/mediabox/Ressources/{audio,images/pochettes}
chmod 755 /var/www/mediabox/Ressources
chmod 775 /var/www/mediabox/Ressources/{audio,images/pochettes}
```

### 2.2 Configuration
1. Copier et adapter le fichier de configuration
```bash
cp config.example.php config.php
```

2. Modifier les paramètres dans config.php :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cda_projet_prod');
define('DB_USER', 'mediabox_user');
define('DB_PASSWORD', 'strong_password_here');
```

### 2.3 Sécurité
1. Vérifier les permissions des fichiers
```bash
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
```

2. Protéger les fichiers sensibles
```bash
chmod 400 config.php
```

3. Configurer le .htaccess
```apache
# Sécurité
Options -Indexes
ServerSignature Off

# Protection XSS et autres
<IfModule mod_headers.c>
    Header set X-XSS-Protection "1; mode=block"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-Content-Type-Options "nosniff"
    Header set Strict-Transport-Security "max-age=31536000; includeSubDomains"
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    # Protection contre les attaques XSS
    Header set X-XSS-Protection "1; mode=block"
    
    # Empêche le site d'être affiché dans un iframe (protection clickjacking)
    Header set X-Frame-Options "SAMEORIGIN"
    
    # Empêche le navigateur de deviner le type MIME
    Header set X-Content-Type-Options "nosniff"
    
    # Force HTTPS pendant 1 an incluant les sous-domaines
    Header set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    
    # Politique de sécurité du contenu
    Header set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; frame-ancestors 'none'; form-action 'self'; base-uri 'self'; object-src 'none'"
</IfModule>

# Redirection vers HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 2.4 Base de données
1. Créer la base de données de production
```sql
CREATE DATABASE cda_projet_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER 'mediabox_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON cda_projet_prod.* TO 'mediabox_user'@'localhost';
FLUSH PRIVILEGES;
```

2. Importer le schéma
```bash
mysql -u mediabox_user -p cda_projet_prod < Database/cda_projet.sql
```

### 2.5 Maintenance
1. Créer un script de maintenance
```bash
touch maintenance.php
chmod 644 maintenance.php
```

2. Contenu du maintenance.php :
```php
<?php
$maintenance = true;
if ($maintenance) {
    header('HTTP/1.1 503 Service Temporarily Unavailable');
    header('Status: 503 Service Temporarily Unavailable');
    header('Retry-After: 3600');
    echo "<h1>Site en maintenance</h1>";
    exit;
}
?>
```

## 3. Monitoring et Logs

### 3.1 Logs Applicatifs
- Vérifier régulièrement /logs/application.log
- Configurer la rotation des logs :
```bash
logrotate -s /var/log/logstatus /etc/logrotate.d/mediabox
```

### 3.2 Surveillance
- Mettre en place des alertes pour :
  - Espace disque > 90%
  - Erreurs PHP critiques
  - Temps de réponse > 2s
  - Tentatives de connexion échouées

## 4. Sauvegarde

### 4.1 Script de Backup
```bash
#!/bin/bash
BACKUP_DIR="/backup/mediabox"
DATE=$(date +%Y%m%d)

# Base de données
mysqldump -u root -p cda_projet_prod > $BACKUP_DIR/db_$DATE.sql

# Fichiers média
tar -czf $BACKUP_DIR/media_$DATE.tar.gz Ressources/

# Nettoyage des vieux backups (garder 7 jours)
find $BACKUP_DIR -mtime +7 -type f -delete
```

### 4.2 Planification
```bash
# Ajouter au crontab
0 2 * * * /scripts/backup_mediabox.sh
```

## 5. Mise à jour

### 5.1 Procédure
1. Activer le mode maintenance
2. Sauvegarder la base et les fichiers
3. Mettre à jour les fichiers
4. Exécuter les migrations
5. Vider le cache
6. Tests de non-régression
7. Désactiver le mode maintenance

### 5.2 Rollback
En cas d'échec :
1. Restaurer la sauvegarde de la base
2. Restaurer les fichiers
3. Vérifier les permissions
4. Désactiver le mode maintenance

## 6. Performance

### 6.1 Configuration PHP
```ini
; php.ini optimisations
memory_limit = 256M
post_max_size = 64M
upload_max_filesize = 64M
max_execution_time = 300
```

### 6.2 Cache
- Activer le cache OpCache
- Configurer le cache des médias dans .htaccess :
```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType audio/mpeg "access plus 1 year"
    ExpiresByType audio/mp4 "access plus 1 year"
</IfModule>
```

## 7. Tests Post-Déploiement

### 7.1 Checklist
- [ ] Connexion utilisateur
- [ ] Upload de fichiers
- [ ] Lecture des médias
- [ ] Formulaires CRUD
- [ ] Sessions et sécurité
- [ ] Performances
- [ ] Logs
- [ ] Sauvegardes

### 7.2 Monitoring
- Mettre en place des outils de surveillance :
  - NewRelic ou équivalent
  - Surveillance des logs
  - Alertes système
