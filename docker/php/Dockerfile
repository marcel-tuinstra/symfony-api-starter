FROM php:8.3-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    libpq \
    postgresql-dev \
    icu-dev \
    libzip-dev \
    oniguruma-dev

# PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql intl opcache zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Symfony CLI (for local DX)
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www/html
COPY . .

CMD ["php-fpm"]
