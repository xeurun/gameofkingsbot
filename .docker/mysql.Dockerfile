ARG VERSION
ARG TIMEZONE

FROM mysql:${VERSION}

COPY /etc/mysql/conf.d/custom.cnf /etc/mysql/conf.d/

RUN chown -R mysql:root /var/lib/mysql/ ; \
    chmod 0644 /etc/mysql/conf.d/custom.cnf

CMD ["mysqld"]

EXPOSE 3306
