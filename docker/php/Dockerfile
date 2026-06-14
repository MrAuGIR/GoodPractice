FROM php:8.2-apache

# Dépendances système
RUN apt-get update && apt-get install -y \
    locales \
    git \
    libicu-dev \
    libpng-dev \
    libzip-dev \
    libxslt-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    unzip \
    curl \
    gnupg \
    && rm -rf /var/lib/apt/lists/*

# Locales
RUN sed -i 's/# en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/' /etc/locale.gen \
    && sed -i 's/# fr_FR.UTF-8 UTF-8/fr_FR.UTF-8 UTF-8/' /etc/locale.gen \
    && locale-gen

ENV LANG=fr_FR.UTF-8
ENV LANGUAGE=fr_FR:fr
ENV LC_ALL=fr_FR.UTF-8

# Extensions PHP (dom, mbstring, fileinfo déjà incluses dans l'image officielle)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    opcache \
    intl \
    zip \
    calendar \
    gd \
    xsl

# Extensions PECL
RUN pecl install apcu xdebug \
    && docker-php-ext-enable apcu

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Apache : mod_rewrite + vhost Symfony (docroot public/)
RUN a2enmod rewrite
COPY vhosts/vhosts.conf /etc/apache2/sites-available/000-default.conf

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www/html
