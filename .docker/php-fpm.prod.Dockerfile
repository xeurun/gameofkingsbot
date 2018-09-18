ARG VERSION
FROM php:${VERSION}

COPY /etc/php/conf.d/custom.ini /usr/local/etc/php/conf.d/
COPY /etc/php/php-fpm.d/ /usr/local/etc/php/php-fpm.d/

RUN apk update ; \
    apk upgrade ; \
    apk add --no-cache shadow gcc make g++ icu-dev zlib-dev autoconf tzdata

# Install extensions
RUN docker-php-ext-install intl zip pcntl pdo pdo_mysql bcmath opcache ; \
    pecl install apcu

RUN echo 'date.timezone=${TZ}' >> /usr/local/etc/php/conf.d/custom.ini ; \
    echo 'opcache.validate_timestamps=0' >> /usr/local/etc/php/conf.d/custom.ini ; \
    ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime ; \
    echo ${TZ} > /etc/timezone ; \
    mkdir -p /app/var/cache ; \
    mkdir -p /app/var/log ; \
    mkdir -p /app/var/sessions ; \
    usermod -u 1000 www-data ; \
    chown -R www-data /app/var

WORKDIR "/app"
