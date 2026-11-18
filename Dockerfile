# Use official PHP 8.2 Apache image
FROM php:8.2-apache

# Install MySQL PDO extension
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

# Copy project files into web root
COPY . /var/www/html/

# Set correct file ownership
RUN chown -R www-data:www-data /var/www/html

# Port exposed by Apache
EXPOSE 80

# Launch Apache in foreground
CMD ["apache2-foreground"]
