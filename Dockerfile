# Use latest PHP version with Apache
FROM php:8.3-apache

# Install needed PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Copy app code
COPY . /var/www/html/

# Copy Composer binary
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN if [ -f "composer.json" ]; then composer install --no-dev --optimize-autoloader || true; fi

# Set correct ownership
RUN chown -R www-data:www-data /var/www/html

# Expose Apache default port (Render maps $PORT to 80 automatically)
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]