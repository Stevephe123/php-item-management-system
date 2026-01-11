FROM php:8.2-apache

# Install PHP extensions needed for MySQL
RUN docker-php-ext-install pdo_mysql mysqli

# Enable Apache rewrite (
RUN a2enmod rewrite
