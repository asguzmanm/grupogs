FROM php:8.0-apache

# Instalar la extensión pdo_mysql
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Instalar las extensiones de PDO para PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copiar el contenido de tu proyecto
COPY . /var/www/html/

EXPOSE 80
