# syntax=docker/dockerfile:1

FROM composer:lts AS deps
WORKDIR /app
RUN --mount=type=bind,source=composer.json,target=composer.json \
    --mount=type=bind,source=composer.lock,target=composer.lock \
    --mount=type=cache,target=/tmp/cache \
    composer install --no-dev --no-interaction

FROM php:8.2-apache AS final

# Activation des extensions PDO MySQL et Apache Rewrite
RUN docker-php-ext-install pdo pdo_mysql && a2enmod rewrite

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=deps /app/vendor/ /var/www/html/vendor
COPY . /var/www/html

# Création et droits d'écriture pour les dossiers d'upload et de logs
RUN mkdir -p /var/www/html/logs \
    /var/www/html/Ressources/audio \
    /var/www/html/Ressources/videos \
    /var/www/html/Ressources/images/photos \
    /var/www/html/Ressources/images/pochettes \
    /var/www/html/Ressources/images/thumbnails \
    && chown -R www-data:www-data /var/www/html/logs /var/www/html/Ressources

USER www-data