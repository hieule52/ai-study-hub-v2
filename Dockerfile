FROM php:8.2-apache

# Install system dependencies for PHP extensions and Composer
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql gd zip

# Enable Apache rewrite, proxy and security headers modules
RUN a2enmod rewrite proxy proxy_http proxy_wstunnel headers

# Copy custom Apache virtual host configuration
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Copy custom PHP configuration
COPY docker/php.ini /usr/local/etc/php/conf.d/uploads.ini

# Install Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html

# Install Composer dependencies
# (We keep dev dependencies if needed, or omit them with --no-dev. Since no require-dev exists, we run standard install)
RUN composer install --no-interaction --optimize-autoloader

# Set permissions for storage and public uploads (crucial for Apache web server write access)
RUN mkdir -p /var/www/html/storage/private/videos \
        /var/www/html/public/uploads/avatars \
        /var/www/html/public/uploads/courses \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/storage /var/www/html/public/uploads

# Expose Web Server (80) and WebSocket Server (8080)
EXPOSE 80 8080

# Default command for Apache
CMD ["apache2-foreground"]
