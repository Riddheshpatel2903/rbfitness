FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev nodejs npm \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN npm install
RUN npm run build

RUN chmod -R 777 storage bootstrap/cache public/build

# Create a blank .env so Laravel can boot (Render env vars override everything)
RUN touch .env

EXPOSE 10000

CMD php artisan optimize:clear && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    php -S 0.0.0.0:10000 -t public