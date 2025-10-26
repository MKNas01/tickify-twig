# Use official PHP 8.2 with Apache
FROM php:8.2-apache

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable Apache mod_rewrite for .htaccess
RUN a2enmod rewrite

# Copy app files to Apache htdocs
WORKDIR /var/www/html
COPY . .

# Install PHP deps with Composer
RUN composer install --no-dev --optimize-autoloader

# Set permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]