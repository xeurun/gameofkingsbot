ARG VERSION
FROM php:${VERSION}

COPY /etc/php/conf.d/ /usr/local/etc/php/conf.d/

RUN apk update ; \
    apk upgrade ; \
    apk add --no-cache shadow gcc make g++ icu-dev zlib-dev autoconf tzdata

# Install extensions (xdebug 2.6.1 have bag)
RUN docker-php-ext-install intl zip pcntl pdo pdo_mysql bcmath ; \
    pecl install xdebug-2.6.0 ; \
    docker-php-ext-enable xdebug

RUN echo 'date.timezone=${TZ}' >> /usr/local/etc/php/conf.d/custom.ini ; \
    echo 'xdebug.remote_host=${XDEBUG_REMOTE_HOST}' >> /usr/local/etc/php/conf.d/xdebug.ini ; \
    echo '0 * * * * php /app/bin/console main-processing' >> /etc/crontabs/www-data ; \
    ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime ; \
    echo ${TZ} > /etc/timezone ; \
    usermod -u 1000 www-data

WORKDIR "/app"

CMD ["crond", "-f", "-L", "/dev/stdout", "-l", "8"]
