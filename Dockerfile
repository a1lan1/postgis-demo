# Here we prepare the base with PHP and all the necessary extensions.
FROM php:8.3-fpm-alpine AS base

LABEL maintainer="<demo@example.com>"

# Install system utilities and dependencies for PHP extensions
RUN apk add --no-cache \
    $PHPIZE_DEPS \
    libzip-dev \
    postgresql-dev \
    libpq \
    oniguruma-dev \
    libxml2-dev \
    supervisor \
    linux-headers \
    nodejs \
    yarn

# Install the PHP extensions required by your project
RUN docker-php-ext-install -j$(nproc) \
    pcntl \
    exif \
    sockets \
    pdo_pgsql \
    zip \
    mbstring \
    bcmath \
    dom \
    xml

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# A separate stage to install Node.js dependencies and cache them.
FROM node:22-alpine AS node_builder

# Set working directory inside the container
WORKDIR /app

COPY package.json yarn.lock ./
RUN yarn install --frozen-lockfile

# Assemble everything into a clean and optimized image.
FROM base AS final
# Set production environment variables
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    DB_CONNECTION=pgsql \
    QUEUE_CONNECTION=redis \
    SESSION_DRIVER=redis \
    CACHE_STORE=redis

# Copy Composer files and install production dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --optimize-autoloader

# Copy the entire application code
COPY . .

# Copy node_modules from the builder stage and then build frontend assets.
# `yarn build` is run here because it requires PHP (for `artisan wayfinder:generate`).
COPY --from=node_builder /app/node_modules ./node_modules
RUN yarn build

# Set correct permissions for storage and cache directories
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Expose the port that Octane will listen on
EXPOSE 8000
