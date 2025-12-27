FROM php:8.4-fpm

# Instalacja zależności systemowych
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    wget \
    libzip-dev \
    libyaml-dev \
    zlib1g-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libpq-dev \
    libssl-dev \
    libicu-dev \
    && docker-php-ext-install intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install xml \
    && docker-php-ext-install pdo_pgsql zip \
    && docker-php-ext-install sockets \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash -

RUN apt-get install -y postgresql-client nodejs

# Konfiguracja Git (zamień na swoje dane)
RUN #git config --global user.name "Jakub Białoń"
RUN #git config --global user.email "bialonj@gmail.com"

# Instalacja Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Instalacja Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

RUN npm install -g yarn

RUN yarn add bootstrap @popperjs/core

# Ustawienie katalogu roboczego
WORKDIR /var/www/html

# Instalacja rozszerzenia YAML
RUN pecl install yaml && docker-php-ext-enable yaml
