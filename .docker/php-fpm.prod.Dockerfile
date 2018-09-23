ARG VERSION
FROM php:${VERSION}

RUN apk update \
    && apk upgrade \
    && apk add --no-cache git gcc make g++ icu-dev zlib-dev libpng-dev jpeg-dev openldap-dev autoconf

# Install extensions
RUN docker-php-ext-install intl ldap zip pcntl mysqli pdo pdo_mysql bcmath ldap intl gd opcache
RUN pecl install apcu

COPY /etc/php/conf.d/custom.ini /usr/local/etc/php/conf.d/
COPY /etc/php/php-fpm.d/custom.conf /usr/local/etc/php/php-fpm.d/

RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone
# docker performance
RUN mkdir -p /var/cache && mkdir -p /var/log && mkdir -p /var/sessions && chown -R www-data /var

WORKDIR "/app"
