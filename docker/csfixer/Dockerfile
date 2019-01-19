FROM php:7.2-fpm

RUN apt-get update && \
    apt-get install git libpq-dev libldap2-dev zlib1g-dev libcurl4-gnutls-dev libicu-dev libmcrypt-dev libxml2-dev g++ -y && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ && \
	docker-php-ext-install -j$(nproc) zip ldap intl curl pdo pdo_mysql pdo_pgsql pgsql bcmath soap zip sockets && \
	pecl install redis apcu && \
    docker-php-ext-enable redis apcu

# Unzip is required for composer.
RUN apt-get update && apt-get install -y unzip

# Install composer
ADD https://getcomposer.org/installer /composer-setup.php

RUN php /composer-setup.php \
    --install-dir=/usr/local/bin/ \
    --filename=composer \
    && rm /composer-setup.php

# Allow Composer to be run as root.
ENV COMPOSER_ALLOW_SUPERUSER 1

ENV PATH="~/.composer/vendor/bin:${PATH}"

RUN composer global require friendsofphp/php-cs-fixer
