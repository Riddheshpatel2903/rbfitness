FROM php:8.4-cli

# Install system + node
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev nodejs npm \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# 🔥 IMPORTANT (THIS WAS MISSING)
RUN npm install
RUN npm run build

# Permissions
RUN chmod -R 777 storage bootstrap/cache

# Laravel setup
RUN cp .env.example .env || true
RUN php artisan key:generate
RUN php artisan config:cache
RUN php artisan migrate --force || true

EXPOSE 10000
CMD php -S 0.0.0.0:10000 -t public