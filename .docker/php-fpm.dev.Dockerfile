ARG VERSION
FROM php:${VERSION}

RUN apk update \
    && apk upgrade \
    && apk add --no-cache gcc make g++ icu-dev zlib-dev libpng-dev jpeg-dev openldap-dev autoconf

# Install extensions
RUN docker-php-ext-install intl zip pcntl pdo pdo_mysql bcmath gd opcache
# 2.6.1 have bag
RUN pecl install xdebug-2.6.0 && docker-php-ext-enable xdebug
RUN pecl install apcu

COPY /etc/php/conf.d/ /usr/local/etc/php/conf.d/
RUN echo 'date.timezone=${TZ}' >> /usr/local/etc/php/conf.d/custom.ini
RUN echo 'xdebug.remote_host=${XDEBUG_REMOTE_HOST}' >> /usr/local/etc/php/conf.d/xdebug.ini

COPY /etc/php/php-fpm.d/ /usr/local/etc/php/php-fpm.d/

RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

# docker performance
RUN mkdir -p /app/var/cache && mkdir -p /app/var/log && mkdir -p /app/var/sessions && chown -R www-data /app/var

WORKDIR "/app"
