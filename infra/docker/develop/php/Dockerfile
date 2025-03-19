# Command : docker buildx build -t backendphp -f ./infra/docker/develop/php/Dockerfile .
# Command : docker run -it --rm backendphp bash
# builder
# https://hub.docker.com/_/composer
FROM composer:lts AS builder

COPY composer.json composer.lock artisan /build/
COPY /bootstrap/ /build/bootstrap/
COPY /config/ /build/config/
COPY /lang/ /build/lang/
COPY /public/ /build/public/
COPY /resources/ /build/resources/
COPY /routes/ /build/routes/
COPY /storage/ /build/storage/
COPY /app/ /build/app/

WORKDIR /build/
RUN composer install -q --no-dev --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist && \
  composer dump-autoload

# runtime
# https://hub.docker.com/r/bitnami/laravel
FROM bitnami/laravel:10.3.3-debian-12-r27 AS runtime

ENV LARAVEL_PORT_NUMBER=80
ENV LARAVEL_SKIP_DATABASE=yes
ENV LARAVEL_SKIP_COMPOSER_UPDATE=yes

COPY --chown=bitnami:bitnami --chmod=750 infra/docker/develop/php/php.dev.ini /opt/bitnami/php/etc/conf.d/
COPY --chown=bitnami:bitnami --chmod=750 --from=builder /build/ /app/

USER bitnami
EXPOSE 80
CMD ["php", "artisan", "migrate", "--force"]
