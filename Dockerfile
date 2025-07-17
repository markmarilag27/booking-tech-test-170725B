# Dockerfile

FROM php:8.2-fpm

# Arguments to match host UID and GID
ARG USER_ID=1000
ARG GROUP_ID=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libonig-dev libxml2-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libpq-dev libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath zip gd intl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create user and group matching host
RUN groupadd -g $GROUP_ID devgroup \
    && useradd -m -u $USER_ID -g devgroup -s /bin/bash devuser \
    && mkdir -p /var/www \
    && chown -R devuser:devgroup /var/www

# Set working directory
WORKDIR /var/www

# Git safe.directory fix
RUN git config --global --add safe.directory /var/www

# Switch to non-root user
USER devuser

EXPOSE 9000
CMD ["php-fpm"]
