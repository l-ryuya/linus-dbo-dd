# # builder ステージ（composer:ltsイメージを使用）
FROM composer:lts AS builder
# Laravelプロジェクトの必要ファイルを /build/ にコピー
COPY composer.json composer.lock .env.develop artisan /build/
COPY app/ /build/app/
COPY bootstrap/ /build/bootstrap/
COPY config/ /build/config/
COPY database/ /build/database/
COPY lang/ /build/lang/
COPY public/ /build/public/
# COPY resources/ /build/resources/  # 必要に応じて有効化してください
COPY routes/ /build/routes/
COPY storage/ /build/storage/

WORKDIR /build/

# Composerで依存インストール（本番用にno-devオプション）
RUN composer install -q --no-dev --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist && \
    composer dump-autoload

FROM php:8.3-apache

RUN a2enmod rewrite
# 下記公式の記述方法
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
# Apache設定ファイル内のDocumentRootを置換
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/../!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    sed -ri 's/<VirtualHost \*:80>/<VirtualHost *:3000>/g' /etc/apache2/sites-available/000-default.conf

COPY infra/docker/develop/php/ports.conf /etc/apache2/ports.conf

COPY infra/docker/develop/php/php.dev.ini /usr/local/etc/php/

RUN apt-get update && \
    apt-get install -y \
    zip unzip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    vim \
    libpq-dev \
    libonig-dev \
    libxml2-dev && \
    docker-php-ext-configure zip && \
    docker-php-ext-install pdo pdo_mysql mysqli zip gd pdo_pgsql && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*


# Laravelプロジェクトのコピー（Laravel全体を /var/www/html に置く場合）
COPY --from=builder /build /var/www/html
WORKDIR /var/www/html

RUN chown -R www-data:www-data storage
