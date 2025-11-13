FROM php:8.3-fpm-alpine

# System dependencies + better shell experience
RUN apk add --no-cache \
    bash \
    bash-completion \
    readline \
    nano \
    git \
    unzip \
    libpq \
    postgresql-dev \
    icu-dev \
    libzip-dev \
    oniguruma-dev

# PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql intl opcache zip

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/php-memory.ini /usr/local/etc/php/conf.d/php-memory.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Symfony CLI (for local DX)
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Configure bash profile with history + completion + sensible defaults
RUN echo "export HISTFILE=/root/.bash_history" >> /root/.bashrc && \
    echo "export HISTSIZE=5000" >> /root/.bashrc && \
    echo "export HISTCONTROL=ignoredups:erasedups" >> /root/.bashrc && \
    echo "[ -f /etc/profile ] && source /etc/profile" >> /root/.bashrc && \
    echo "source /usr/share/bash-completion/bash_completion" >> /root/.bashrc && \
    echo 'PS1="\\u@php-container:\\w$ "' >> /root/.bashrc

# 🧩 Xdebug (enabled only when requested)
RUN apk add --no-cache $PHPIZE_DEPS linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "zend_extension=xdebug" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=${XDEBUG_MODE:-off}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log_level=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host=true" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/html
COPY . .

# Environment variables
ENV APP_ENV=dev
ENV XDEBUG_MODE=off
ENV XDEBUG_CONFIG="client_host=host.docker.internal"

CMD ["php-fpm"]
