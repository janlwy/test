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
