# httpd:2.4
FROM httpd@sha256:fbc12199ccad031d8047e9c789d65aceee2d14f99ba90664cd3a3996867a5582

RUN apt-get update && \
    apt-get install -y --no-install-recommends curl && \
    rm -rf /var/lib/apt/lists/*

COPY ../../../../ /var/www/html/
COPY infra/docker/develop2/apache/conf/httpd.conf /usr/local/apache2/conf/httpd.conf

RUN chown -R www-data:www-data /var/www/html
RUN chown -R www-data:www-data /var/www/html /usr/local/apache2/logs
USER www-data
