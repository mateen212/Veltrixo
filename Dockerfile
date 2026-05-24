FROM php:8.4-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    git curl libpng-dev libjpeg-turbo-dev libwebp-dev freetype-dev \
    libzip-dev zip unzip oniguruma-dev icu-dev linux-headers \
    supervisor nginx nodejs npm

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install \
    pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache sockets

# Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/Veltrixo

# Copy application
COPY . .

# Install PHP deps (production)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node deps and build assets
RUN npm ci && npm run build && rm -rf node_modules

# Permissions
RUN chown -R www-data:www-data /var/www/Veltrixo \
 && chmod -R 755 /var/www/Veltrixo/storage \
 && chmod -R 755 /var/www/Veltrixo/bootstrap/cache

# PHP OPcache config
RUN { \
    echo "opcache.enable=1"; \
    echo "opcache.memory_consumption=256"; \
    echo "opcache.max_accelerated_files=20000"; \
    echo "opcache.validate_timestamps=0"; \
} > /usr/local/etc/php/conf.d/opcache.ini

# Copy configs
COPY docker/nginx/Veltrixo.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

EXPOSE 80 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
