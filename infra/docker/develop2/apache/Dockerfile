FROM httpd:2.4-slim

# 必要なら curl だけ入れる（極力省く）
RUN apt-get update && \
    apt-get install -y --no-install-recommends curl && \
    rm -rf /var/lib/apt/lists/*


COPY ../../../../ /var/www/html/
COPY infra/docker/develop2/apache/conf/httpd.conf /usr/local/apache2/conf/httpd.conf
