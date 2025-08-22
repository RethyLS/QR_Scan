# 1️⃣ Base image with PHP 8.2 and FPM
FROM php:8.2-fpm

# 2️⃣ Set working directory
WORKDIR /var/www/html

# 3️⃣ Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    curl \
    zip \
    && docker-php-ext-install pdo pdo_pgsql zip

# 4️⃣ Install Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# 5️⃣ Copy composer files first (for caching)
COPY composer.json composer.lock ./

# 6️⃣ Copy .env.example as .env to prevent artisan errors
COPY .env.example .env

# 7️⃣ Install PHP dependencies WITHOUT running scripts
RUN composer install --no-dev --no-scripts --optimize-autoloader

# 8️⃣ Copy the rest of the application
COPY . .

# 9️⃣ Fix permissions for Laravel storage and cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# 🔟 Run Laravel scripts manually to avoid errors during build
RUN php artisan config:clear || true
RUN php artisan route:clear || true
RUN php artisan view:clear || true
RUN php artisan key:generate --force

# 1️⃣1️⃣ Expose port 8000
EXPOSE 8000

# 1️⃣2️⃣ Set Laravel start command
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
