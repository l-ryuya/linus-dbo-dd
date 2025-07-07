# builder ステージ（composer:ltsイメージを使用）
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


FROM php:8.4-apache

#  Apacheのmod_rewrite（URL書き換え）モジュールを有効化
RUN cd /etc/apache2/mods-enabled \
  && ln -s ../mods-available/rewrite.load

#  カスタムPHP設定とApacheのバーチャルホスト設定を追加
ADD infra/docker/develop/php/php.dev.ini /usr/local/etc/php/
ADD infra/docker/develop/php/000-default.conf /etc/apache2/sites-enabled/

RUN apt-get update \
  && apt-get install -y \
    git \
    zip unzip \
    libzip-dev \
    vim \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
  && docker-php-ext-configure zip \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install \
    pdo pdo_mysql mysqli zip gd pdo_pgsql

#  GD（画像処理ライブラリ）をJPEG/Freetype対応でビルド
RUN docker-php-ext-configure gd \
  --with-freetype=/usr/include/ \
  --with-jpeg=/usr/include \
  && docker-php-ext-install -j$(nproc) gd

#  Composerのインストール（古典的な方法）
# 後で COPY --from=composer:latest でも上書きされるので冗長かも
# RUN cd /usr/bin \
#   && curl -s http://getcomposer.org/installer | php \
#   && ln -s /usr/bin/composer.phar /usr/bin/composer

# #  Composerの環境変数と実行ファイルのコピー（推奨のインストール方法）
# ENV COMPOSER_ALLOW_SUPERUSER 1
# ENV COMPOSER_HOME /composer
# ENV PATH $PATH:/composer/vendor/bin

# #  最新のcomposerバイナリを別ステージからコピー（推奨）
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#  Composerのバージョンを2に固定
# RUN composer self-update --2

#  Node.js 20.x のインストール（最新のLTS対応）
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs


#  npm環境構築（フロントビルドツール用）
RUN rm -rf node_modules                              # node_modules削除（前段のゴミ削除）
RUN npm install -g npm n cross-env                   # npm自体 + cross-envなどツールをグローバルに
RUN npm cache clear --force                          # npmキャッシュ削除でクリーンな状態に
RUN npm install webpack --save                       # webpackインストール（ローカルプロジェクト想定）
RUN npm install node-sass --nodedir=/usr/include/node # node-sassビルド用にnodeヘッダ指定

# Laravelプロジェクトのコピー（Laravel全体を /var/www/html に置く場合）
COPY ./ /var/www/html
# vendorだけは builder から持ってくる
COPY --from=builder /build/vendor /var/www/html/vendor
WORKDIR /var/www/html


# # .envファイルのコピーは composer install より前に
# COPY ../example-api/env.example .env

# # Laravel依存のインストールと準備
# RUN composer install --no-dev --optimize-autoloader


# # # Command : docker buildx build -t backendphp -f ./infra/docker/develop/php/Dockerfile .
# # Command : docker run -it --rm backendphp bash
# # builder
# # https://hub.docker.com/_/composer
# FROM composer:lts AS builder

# COPY composer.json composer.lock .env.develop artisan /build/
# COPY /app/ /build/app/
# COPY /bootstrap/ /build/bootstrap/
# COPY /config/ /build/config/
# COPY /database/ /build/database/
# COPY /lang/ /build/lang/
# COPY /public/ /build/public/
# # COPY /resources/ /build/resources/
# COPY /routes/ /build/routes/
# COPY /storage/ /build/storage/

# WORKDIR /build/
# RUN composer install -q --no-dev --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist && \
#   composer dump-autoload

# # runtime
# # https://hub.docker.com/r/bitnami/laravel
# FROM bitnami/laravel:10.3.3-debian-12-r27 AS runtime

# ENV LARAVEL_PORT_NUMBER=3000
# ENV LARAVEL_SKIP_DATABASE=yes
# ENV LARAVEL_SKIP_COMPOSER_UPDATE=yes

# RUN chown -R bitnami:bitnami /opt/bitnami/
# RUN chmod -R 750 /opt/bitnami/

# COPY --chown=bitnami:bitnami --chmod=750 infra/docker/develop/php/php.dev.ini /opt/bitnami/php/etc/conf.d/
# COPY --chown=bitnami:bitnami --chmod=750 --from=builder /build/ /app/

# RUN chown -R bitnami:bitnami /app/

# # supervisorをインストール
# RUN install_packages supervisor procps

# COPY infra/docker/develop/php/supervisord.conf /etc/supervisor/
# COPY infra/docker/develop/php/laravel-worker.conf /etc/supervisor/conf.d/
# RUN chown bitnami:bitnami /var/log/supervisor && chmod 740 /var/log/supervisor
# COPY infra/docker/develop/php/scheduler.sh /app/scheduler.sh
# RUN chown bitnami:bitnami /app/scheduler.sh && chmod +x /app/scheduler.sh

# # entrypoint.sh を上書きして supervisord を起動
# COPY --chown=bitnami:bitnami --chmod=750 infra/docker/develop/php/entrypoint-overwrite.sh /opt/bitnami/scripts/laravel/entrypoint.sh

# USER bitnami
# # EXPOSE 8080
