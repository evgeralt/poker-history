FROM php:7.4.25-fpm
# Install modules

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        zlib1g-dev \
        libicu-dev \
        git \
        libzip-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_mysql mysqli zip bcmath \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install sockets

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

EXPOSE 9000
EXPOSE 18999

WORKDIR /var/www

CMD ["php-fpm"]
