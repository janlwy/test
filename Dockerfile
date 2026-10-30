# syntax=docker/dockerfile:1

FROM php:8.4-apache-trixie AS final

# Mise à jour de sécurité et installation de l'extension PDO MySQL
RUN apt-get update && apt-get upgrade -y \
    && docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Configuration PHP de production + augmentation de la taille d'upload (Audio/Vidéo/Photos)
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && printf "upload_max_filesize = 100M\npost_max_size = 105M\nmemory_limit = 256M\n" > "$PHP_INI_DIR/conf.d/uploads.ini"

WORKDIR /var/www/html
COPY . /var/www/html

RUN mkdir -p /var/www/html/logs \
    /var/www/html/Ressources/audio \
    /var/www/html/Ressources/videos \
    /var/www/html/Ressources/images/photos \
    /var/www/html/Ressources/images/pochettes \
    /var/www/html/Ressources/images/thumbnails \
    && chown -R www-data:www-data /var/www/html/logs /var/www/html/Ressources

USER www-data