FROM httpd:2.4

RUN apt-get update && apt-get install -y \
    iputils-ping \
    dnsutils \
    curl \
    net-tools \
    procps \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

COPY ../../../../ /var/www/html/
COPY infra/docker/develop/apache/conf/httpd.conf /usr/local/apache2/conf/httpd.conf
