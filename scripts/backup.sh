#!/bin/bash

# Configuration
BACKUP_DIR="/var/backups/mediabox"
DB_USER=${DB_USER:-"root"}
DB_PASS=${DB_PASSWORD:-""}
DB_NAME=${DB_NAME:-"cda_projet"}
DATE=$(date +%Y%m%d_%H%M%S)

# Création des répertoires
mkdir -p "$BACKUP_DIR/database"
mkdir -p "$BACKUP_DIR/media"

# Backup de la base de données
mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_DIR/database/db_backup_$DATE.sql"

# Compression des fichiers média
tar -czf "$BACKUP_DIR/media/media_backup_$DATE.tar.gz" Ressources/

# Nettoyage des vieux backups (conservation 7 jours)
find "$BACKUP_DIR" -name "*.sql" -mtime +7 -delete
find "$BACKUP_DIR" -name "*.tar.gz" -mtime +7 -delete

# Log du backup
echo "Backup completed at $DATE" >> "$BACKUP_DIR/backup.log"
