# Stage 1: Build Assets
FROM oven/bun:latest AS asset-builder
WORKDIR /app
COPY package.json bun.lock ./
RUN bun install --frozen-lockfile
COPY . .
RUN bun run build

# Stage 2: Application
FROM php:8.4-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    bash \
    git \
    linux-headers

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        bcmath \
        gd \
        zip \
        intl \
        pcntl \
        opcache \
        mbstring \
    && docker-php-ext-enable opcache

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Copy built assets from builder stage
COPY --from=asset-builder /app/public/build ./public/build

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# PHP-FPM default configuration
COPY .docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Setup Entrypoint for Auto-Migration
COPY .docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
EXPOSE 9000
CMD ["php-fpm"]
