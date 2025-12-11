# syntax = docker/dockerfile:experimental

ARG PHP_VERSION=8.2
ARG NODE_VERSION=18

FROM ubuntu:22.04 as base
LABEL fly_launch_runtime="laravel"

# Install basics
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && \
    apt-get install -y \
    software-properties-common \
    gnupg \
    curl \
    ca-certificates \
    zip \
    unzip \
    git \
    supervisor \
    sqlite3 \
    libcap2-bin \
    libpng-dev \
    python2 \
    dnsutils \
    librsvg2-bin \
    fswatch \
    ffmpeg \
    nano \
    cron \
    && add-apt-repository ppa:ondrej/php -y \
    && apt-get update \
    && apt-get install -y \
    php${PHP_VERSION} \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-sqlite3 \
    && mkdir -p /run/php \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Copy Configs
COPY .fly/entrypoint.sh /entrypoint
COPY .fly/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY .fly/nginx.conf /etc/nginx/sites-available/default
COPY .fly/php-fpm.conf /etc/php/${PHP_VERSION}/fpm/php-fpm.conf

RUN chmod +x /entrypoint

# Setup Application
WORKDIR /var/www/html
COPY . .

# Install PHP Dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Install JS Dependencies & Build
RUN npm install && npm run build

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

EXPOSE 8080

ENTRYPOINT ["/entrypoint"]
