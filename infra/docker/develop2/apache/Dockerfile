# httpd:2.4
FROM httpd@sha256:fbc12199ccad031d8047e9c789d65aceee2d14f99ba90664cd3a3996867a5582

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        curl \
        procps && \
    rm -rf /var/lib/apt/lists/*

COPY composer.json composer.lock .env.develop artisan /var/www/html/

COPY /app/ /var/www/html/app/
COPY /bootstrap/ /var/www/html/bootstrap/
COPY /config/ /var/www/html/config/
COPY /database/ /var/www/html/database/
COPY /lang/ /var/www/html/lang/
COPY /public/ /var/www/html/public/
COPY ./routes/ /var/www/html/routes/
COPY ./storage/ /var/www/html/storage/

COPY infra/docker/develop2/apache/conf/httpd.conf /usr/local/apache2/conf/httpd.conf
