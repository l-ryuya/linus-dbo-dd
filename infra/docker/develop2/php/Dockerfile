# builder ステージ（composer:ltsイメージを使用）
FROM composer@sha256:e27f788e74d2233af99d51b8ef9a086ab6158890d63102e6696db165e0d36403 AS builder
# Laravelプロジェクトの必要ファイルを /build/ にコピー
COPY composer.json composer.lock .env.develop artisan /build/
COPY /app/ /build/app/
COPY /bootstrap/ /build/bootstrap/
COPY /config/ /build/config/
COPY /database/ /build/database/
COPY /lang/ /build/lang/
COPY /public/ /build/public/
COPY /resources/ /build/resources/
COPY ./routes/ /build/routes/
COPY ./storage/ /build/storage/

WORKDIR /build/

# Composerで依存インストール（本番用にno-devオプション）
RUN composer install -q --no-dev --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist && \
    composer dump-autoload

# php:8.3-fpm
FROM php@sha256:b258e182312129e299e69714aa14417760dfd746d7567020ecfe4440d37b7b76

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libpq5 \
    libonig-dev \
    supervisor \
    procps && \
    docker-php-ext-install \
        pcntl \
        pdo_pgsql \
        mbstring && \
    apt-get purge -y --auto-remove libpq-dev libonig-dev && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

COPY infra/docker/develop2/php/php.dev.ini /usr/local/etc/php/
COPY infra/docker/develop2/php/www.conf /usr/local/etc/php-fpm.d/www.conf

COPY --from=builder /build /var/www/html
WORKDIR /var/www/html

RUN chown -R www-data:www-data storage

COPY infra/docker/develop2/php/supervisord.conf /etc/supervisor/
COPY infra/docker/develop2/php/laravel-worker.conf /etc/supervisor/conf.d/
RUN chown www-data:www-data /var/log/supervisor && chmod 740 /var/log/supervisor
COPY infra/docker/develop2/php/entrypoint-overwrite.sh /usr/local/bin/entrypoint-overwrite.sh
RUN chmod +x /usr/local/bin/entrypoint-overwrite.sh

CMD ["/usr/local/bin/entrypoint-overwrite.sh"]
