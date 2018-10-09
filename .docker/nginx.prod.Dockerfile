FROM nginx:alpine

COPY /etc/nginx/nginx.conf /etc/nginx/
COPY /etc/nginx/conf.d/prod.conf /etc/nginx/conf.d/

RUN apk update ; \
    apk upgrade ; \
    adduser -D -H -u 1000 -s /bin/sh www-data ; \
    rm /etc/nginx/conf.d/default.conf ; \
    ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime ; \
    echo ${TZ} > /etc/timezone

CMD ["nginx"]

EXPOSE 80 443
