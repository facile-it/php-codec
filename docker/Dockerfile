ARG PHP_IMAGE
FROM $PHP_IMAGE

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update
RUN apt-get -y install git zip libxml2-dev
RUN docker-php-ext-install xml dom

ARG XDEBUG=xdebug
RUN pecl install ${XDEBUG}
RUN docker-php-ext-enable xdebug

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN useradd -ms /bin/bash dev
USER dev
WORKDIR /home/dev/lib
