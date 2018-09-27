ARG VERSION
FROM php:${VERSION}

RUN apk update \
    && apk upgrade \
    && apk add --no-cache gcc make g++ icu-dev zlib-dev libpng-dev jpeg-dev openldap-dev autoconf

# Install extensions
RUN docker-php-ext-install intl zip pcntl pdo pdo_mysql bcmath gd opcache
RUN pecl install apcu

COPY /etc/php/conf.d/custom.ini /usr/local/etc/php/conf.d/
RUN echo 'date.timezone=${TZ}' > /usr/local/etc/php/conf.d/custom.ini
RUN echo 'opcache.validate_timestamps=0' > /usr/local/etc/php/conf.d/custom.ini

COPY /etc/php/php-fpm.d/ /usr/local/etc/php/php-fpm.d/

RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone
# docker performance
RUN mkdir -p /app/var/cache && mkdir -p /app/var/log && mkdir -p /app/var/sessions && chown -R www-data /app/var

WORKDIR "/app"
