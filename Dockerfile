# 1️⃣ Base image with PHP 8.2 and FPM
FROM php:8.2-fpm

# 2️⃣ Set working directory
WORKDIR /var/www/html

# 3️⃣ Install system dependencies
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

# 6️⃣ Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# 7️⃣ Copy the rest of the application
COPY . .

# 8️⃣ Expose port 8000
EXPOSE 8000

# 9️⃣ Set Laravel start command
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
