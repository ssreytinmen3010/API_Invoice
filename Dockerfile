# ───────────────────────────────────────────────────────────────
# Stage 1: Install Composer dependencies (cached & fast)
# ───────────────────────────────────────────────────────────────
FROM composer:latest AS composer

WORKDIR /app

# Copy only dependency files first → great caching
COPY composer.json composer.lock* ./

# Fixed: proper line continuations, no trailing spaces after \
RUN --mount=type=cache,target=/tmp/composer-cache \
    COMPOSER_PROCESS_TIMEOUT=1200 \
    composer install \
    --prefer-dist \
    --no-dev \
    --no-scripts \
    --no-plugins \
    --no-interaction \
    --no-autoloader \
    --optimize-autoloader \
    --verbose

# ───────────────────────────────────────────────────────────────
# Stage 2: Final runtime image
# ───────────────────────────────────────────────────────────────
FROM php:8.2-apache

# Install system dependencies + PostgreSQL + GD
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    pkg-config \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_pgsql pgsql mbstring zip gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy Composer-installed vendor + composer files from stage 1
COPY --from=composer /app/vendor/ ./vendor/
COPY --from=composer /app/composer.* ./

# Copy the full application code
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload \
    --no-dev \
    --classmap-authoritative \
    --optimize \
    --no-interaction

# Set permissions for Laravel/Symfony
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Apache: set DocumentRoot to /public + fix <Directory>
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/c\<Directory /var/www/html/public>\n    Options Indexes FollowSymLinks\n    AllowOverride All\n    Require all granted\n</Directory>' /etc/apache2/sites-available/000-default.conf

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
