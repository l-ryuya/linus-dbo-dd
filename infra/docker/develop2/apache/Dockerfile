FROM httpd:2.4

# パッケージリスト更新＆curlインストール
RUN apt-get update && apt-get install -y curl && rm -rf /var/lib/apt/lists/*

COPY ../../../../ /var/www/html/
COPY infra/docker/develop2/apache/conf/httpd.conf /usr/local/apache2/conf/httpd.conf
