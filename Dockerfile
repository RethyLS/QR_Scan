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

# 6️⃣ Copy .env.example as .env to prevent artisan errors during composer install
COPY .env.example .env

# 7️⃣ Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# 8️⃣ Copy the rest of the application
COPY . .

# 9️⃣ Fix permissions for Laravel storage and cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# 🔟 Expose port 8000
EXPOSE 8000

# 1️⃣1️⃣ Set Laravel start command
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
