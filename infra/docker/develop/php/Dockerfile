# # builder ステージ（composer:ltsイメージを使用）
FROM composer:lts AS builder
# Laravelプロジェクトの必要ファイルを /build/ にコピー
COPY composer.json composer.lock .env.develop artisan /build/
COPY /app/ /build/app/
COPY /bootstrap/ /build/bootstrap/
COPY /config/ /build/config/
COPY /database/ /build/database/
COPY /lang/ /build/lang/
COPY /public/ /build/public/
COPY ./routes/ /build/routes/
COPY ./storage/ /build/storage/

WORKDIR /build/

# Composerで依存インストール（本番用にno-devオプション）
RUN composer install -q --no-dev --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist && \
    composer dump-autoload
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libonig-dev && \
    docker-php-ext-install \
        pdo \
        pdo_pgsql \
        mbstring && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

COPY infra/docker/develop/php/php.dev.ini /usr/local/etc/php/
COPY infra/docker/develop/php/www.conf /usr/local/etc/php-fpm.d/www.conf

COPY --from=builder /build /var/www/html
WORKDIR /var/www/html

RUN chown -R www-data:www-data storage
