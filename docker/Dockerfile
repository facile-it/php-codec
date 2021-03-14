FROM php:7.2

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update \
    && apt-get -y install \
        git \
        zip \
    && apt-get -y install libxml2-dev \
    && docker-php-ext-install \
        xml \
        dom \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN useradd -ms /bin/bash dev
USER dev
WORKDIR /home/dev/lib
