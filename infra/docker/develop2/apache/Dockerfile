# httpd:2.4
FROM httpd@sha256:fbc12199ccad031d8047e9c789d65aceee2d14f99ba90664cd3a3996867a5582

RUN apt-get update && \
    apt-get install -y --no-install-recommends curl && \
    rm -rf /var/lib/apt/lists/*
COPY ../../../../app            /var/www/html
COPY ../../../../bootstrap      /var/www/html
COPY ../../../../config         /var/www/html
COPY ../../../../database       /var/www/html
COPY ../../../../public         /var/www/html
COPY ../../../../routes         /var/www/html
COPY ../../../../storage        /var/www/html
COPY ../../../../vendor         /var/www/html
COPY ../../../../artisan        /var/www/html
COPY ../../../../composer.json  /var/www/html
COPY ../../../../composer.lock  /var/www/html
COPY ../../../../.env.develop   /var/www/html

COPY infra/docker/develop2/apache/conf/httpd.conf /usr/local/apache2/conf/httpd.conf
