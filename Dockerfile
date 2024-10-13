# Utilisation de l'image officielle PHP avec les extensions nécessaires pour Laravel
FROM php:8.2-fpm

# Installer des dépendances système et les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql pdo_pgsql

# Installer les extensions manquantes : pcntl, mongodb, exif
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install exif \
    && docker-php-ext-enable exif

ENV COMPOSER_ALLOW_SUPERUSER 1

# Installer Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copier le code source de Laravel dans le conteneur
COPY . /var/www/html

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer les dépendances PHP
RUN composer install --optimize-autoloader --no-dev

# Donner les permissions nécessaires
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port 9000 (PHP-FPM)
EXPOSE 9000

# Démarrer PHP-FPM
CMD ["php-fpm"]
