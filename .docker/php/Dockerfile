FROM php:8.3-apache

# Installer extensions PHP nécessaires et mod_rewrite
RUN apt-get update && apt-get install -y     libzip-dev     zip     unzip     && docker-php-ext-install pdo pdo_mysql     && a2enmod rewrite     && rm -rf /var/lib/apt/lists/*

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
