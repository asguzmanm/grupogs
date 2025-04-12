FROM php:8.0-apache

# Instalar la extensión pdo_mysql
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copiar el contenido de tu proyecto
COPY . /var/www/html/

EXPOSE 80
