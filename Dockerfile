FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN chmod -R 777 storage bootstrap/cache

RUN cp .env.example .env || true
RUN php artisan key:generate
RUN php artisan config:cache
RUN php artisan migrate --force || true

EXPOSE 10000

CMD php -S 0.0.0.0:10000 -t public