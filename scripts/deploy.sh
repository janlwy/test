#!/bin/bash

# Variables
PROD_DIR="/var/www/mediabox"
BACKUP_DIR="/backup/mediabox"
DATE=$(date +%Y%m%d_%H%M%S)
LOG_FILE="/var/log/mediabox/deploy_$DATE.log"

# Fonctions
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a $LOG_FILE
}

error_exit() {
    log "ERROR: $1"
    exit 1
}

# Début du déploiement
log "Début du déploiement"

# 1. Backup
log "Création de la sauvegarde..."
mysqldump -u root -p cda_projet_prod > $BACKUP_DIR/db_$DATE.sql || error_exit "Échec du backup DB"
tar -czf $BACKUP_DIR/files_$DATE.tar.gz $PROD_DIR || error_exit "Échec du backup fichiers"

# 2. Mode maintenance
log "Activation du mode maintenance..."
cp maintenance.php $PROD_DIR/index.php || error_exit "Échec activation maintenance"

# 3. Mise à jour des fichiers
log "Mise à jour des fichiers..."
rsync -av --exclude 'config.php' --exclude 'logs/*' --exclude 'Ressources/*' ./ $PROD_DIR/ || error_exit "Échec rsync"

# 4. Permissions
log "Mise à jour des permissions..."
find $PROD_DIR -type f -exec chmod 644 {} \;
find $PROD_DIR -type d -exec chmod 755 {} \;
chmod 775 $PROD_DIR/Ressources/{audio,images/pochettes}
chmod 400 $PROD_DIR/config.php

# 5. Base de données
log "Mise à jour de la base de données..."
php $PROD_DIR/Database/migrate.php || error_exit "Échec des migrations"

# 6. Tests
log "Exécution des tests..."
php $PROD_DIR/vendor/bin/phpunit || error_exit "Échec des tests"

# 7. Cache
log "Nettoyage du cache..."
php $PROD_DIR/cache/clear.php || error_exit "Échec nettoyage cache"

# 8. Désactivation maintenance
log "Désactivation du mode maintenance..."
mv $PROD_DIR/index.php.bak $PROD_DIR/index.php || error_exit "Échec désactivation maintenance"

log "Déploiement terminé avec succès"
