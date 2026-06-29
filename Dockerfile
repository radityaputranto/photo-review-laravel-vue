# ---------------------------------------------------------
# Stage 1: Build Vue/Vite Assets
# ---------------------------------------------------------
FROM node:20-alpine as node-build
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---------------------------------------------------------
# Stage 2: Install PHP Dependencies
# ---------------------------------------------------------
FROM composer:2.7 as vendor-build
WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --ignore-platform-reqs --no-scripts

# ---------------------------------------------------------
# Stage 3: Production Image
# ---------------------------------------------------------
FROM php:8.2-fpm-alpine

# Install system dependencies & PHP extensions needed for Laravel
RUN apk add --no-cache \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    zlib-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip opcache

# Set working directory
WORKDIR /var/www/html

# Copy PHP dependencies
COPY --from=vendor-build /app/vendor/ /var/www/html/vendor/

# Copy application files
COPY . .

# Copy compiled assets from node
COPY --from=node-build /app/public/build/ /var/www/html/public/build/

# Set permissions for storage and bootstrap cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000 for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
