# Command : docker buildx build -t backendphp -f ./infra/docker/bitnami/php/Dockerfile .
# Command : docker run -it --rm backendphp bash
# builder
# https://hub.docker.com/_/composer
FROM composer:lts AS builder

COPY composer.json composer.lock .env.develop artisan /build/
COPY /app/ /build/app/
COPY /bootstrap/ /build/bootstrap/
COPY /config/ /build/config/
COPY /database/ /build/database/
COPY /lang/ /build/lang/
COPY /public/ /build/public/
# COPY /resources/ /build/resources/
COPY /routes/ /build/routes/
COPY /storage/ /build/storage/

WORKDIR /build/
RUN composer install -q --no-dev --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist && \
  composer dump-autoload

# runtime
# https://hub.docker.com/r/bitnami/laravel
FROM bitnami/laravel:10.3.3-debian-12-r27 AS runtime

ENV LARAVEL_PORT_NUMBER=3000
ENV LARAVEL_SKIP_DATABASE=yes
ENV LARAVEL_SKIP_COMPOSER_UPDATE=yes

RUN chown -R bitnami:bitnami /opt/bitnami/
RUN chmod -R 750 /opt/bitnami/

COPY --chown=bitnami:bitnami --chmod=750 infra/docker/develop/php/php.dev.ini /opt/bitnami/php/etc/conf.d/
COPY --chown=bitnami:bitnami --chmod=750 --from=builder /build/ /app/

RUN chown -R bitnami:bitnami /app/

# supervisorをインストール
RUN install_packages supervisor procps

COPY infra/docker/develop/php/supervisord.conf /etc/supervisor/
COPY infra/docker/develop/php/laravel-worker.conf /etc/supervisor/conf.d/
RUN chown bitnami:bitnami /var/log/supervisor && chmod 740 /var/log/supervisor
COPY infra/docker/develop/php/scheduler.sh /app/scheduler.sh
RUN chown bitnami:bitnami /app/scheduler.sh && chmod +x /app/scheduler.sh

# entrypoint.sh を上書きして supervisord を起動
COPY --chown=bitnami:bitnami --chmod=750 infra/docker/develop/php/entrypoint-overwrite.sh /opt/bitnami/scripts/laravel/entrypoint.sh

USER bitnami
# EXPOSE 8080
