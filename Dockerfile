# Build assets with Node
FROM node:20 AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Final PHP image
FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Copy built assets from Node runner
COPY --from=node-builder /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader

RUN chmod -R 777 storage bootstrap/cache public/build
RUN cp .env.example .env || true
RUN php artisan key:generate
RUN php artisan config:cache
# Migrations are better run during deployment, but keeping it as requested
RUN php artisan migrate --force || true

EXPOSE 10000

CMD php -S 0.0.0.0:10000 -t public