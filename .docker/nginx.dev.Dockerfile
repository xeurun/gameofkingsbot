FROM nginx:alpine

RUN rm /etc/nginx/conf.d/default.conf
RUN mkdir -p /etc/nginx/snippets/ && touch /etc/nginx/snippets/letsencrypt-acme-challenge.conf

COPY /etc/nginx/nginx.conf /etc/nginx/
COPY /etc/nginx/conf.d/dev.conf /etc/nginx/conf.d/

RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

RUN apk update \
    && apk upgrade \
    && adduser -D -H -u 1000 -s /bin/sh www-data

CMD ["nginx"]

EXPOSE 80 443
