ARG VERSION
FROM php:${VERSION}

COPY /etc/php/conf.d/custom.ini /usr/local/etc/php/conf.d/

RUN apk update ; \
    apk upgrade ; \
    apk add --no-cache shadow gcc make g++ icu-dev zlib-dev autoconf tzdata

# Install extensions
RUN docker-php-ext-install intl zip pcntl pdo pdo_mysql bcmath opcache ; \
    pecl install apcu

RUN echo 'date.timezone=${TZ}' >> /usr/local/etc/php/conf.d/custom.ini ; \
    echo 'opcache.validate_timestamps=0' >> /usr/local/etc/php/conf.d/custom.ini ; \
    echo '0 * * * * php /app/bin/console main-processing' >> /etc/crontabs/www-data ; \
    ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime ; \
    echo ${TZ} > /etc/timezone ; \
    usermod -u 1000 www-data

WORKDIR "/app"

CMD ["crond", "-f", "-L", "/dev/stdout", "-l", "8"]
