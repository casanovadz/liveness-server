# Use PHP with Apache
FROM php:8.1-apache

# Fix Apache ServerName warning
RUN echo "ServerName liveness-bls.uk" >> /etc/apache2/apache2.conf

# Install dependencies
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Configure PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_sqlite \
    gd \
    zip \
    opcache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Tune PHP for better performance
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=60'; \
} >> /usr/local/etc/php/conf.d/opcache-recommended.ini

# Optimize Apache for low-resource environments
RUN { \
    echo '<IfModule mpm_prefork_module>'; \
    echo '  StartServers            2'; \
    echo '  MinSpareServers         1'; \
    echo '  MaxSpareServers         2'; \
    echo '  MaxRequestWorkers       5'; \
    echo '  MaxConnectionsPerChild  1000'; \
    echo '</IfModule>'; \
    echo 'Timeout 300'; \
    echo 'KeepAlive On'; \
    echo 'MaxKeepAliveRequests 100'; \
    echo 'KeepAliveTimeout 5'; \
} >> /etc/apache2/conf-available/docker-php.conf

# Copy application files
COPY src/ /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]