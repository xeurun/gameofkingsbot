ARG VERSION
FROM php:${VERSION}

RUN apk update \
    && apk upgrade \
    && apk add --no-cache gcc make g++ icu-dev zlib-dev libpng-dev jpeg-dev openldap-dev autoconf tzdata

# Install extensions (xdebug 2.6.1 have bag)
RUN docker-php-ext-install intl zip pcntl pdo pdo_mysql bcmath gd opcache \
    && pecl install xdebug-2.6.0 \
    && docker-php-ext-enable xdebug \
    && pecl install apcu

COPY /etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY /etc/php/php-fpm.d/ /usr/local/etc/php/php-fpm.d/

RUN echo 'date.timezone=${TZ}' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'xdebug.remote_host=${XDEBUG_REMOTE_HOST}' >> /usr/local/etc/php/conf.d/xdebug.ini \
    && crontab -u root -l \
    && echo '0 */1 * * * php /app/bin/console main-processing' >> /etc/crontabs/root \
    && ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime \
    && echo ${TZ} > /etc/timezone \
    && mkdir -p /app/var/cache \
    && mkdir -p /app/var/log \
    && mkdir -p /app/var/sessions \
    && chown -R www-data /app/var

WORKDIR "/app"
