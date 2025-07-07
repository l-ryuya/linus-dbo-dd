# # builder ステージ（composer:ltsイメージを使用）
FROM composer:lts AS builder

# # Laravelプロジェクトの必要ファイルを /build/ にコピー
# COPY composer.json composer.lock .env.develop artisan /build/
# COPY bootstrap/ /build/bootstrap/
# COPY config/ /build/config/
# COPY database/ /build/database/
# COPY lang/ /build/lang/
# COPY public/ /build/public/
# # COPY resources/ /build/resources/  # 必要に応じて有効化してください
# COPY routes/ /build/routes/
# COPY storage/ /build/storage/

COPY ./ /app/
WORKDIR /app

# Composerで依存インストール（本番用にno-devオプション）
RUN composer install -q --no-dev --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist && \
    composer dump-autoload


FROM php:8.4-apache

#  Apacheのmod_rewrite（URL書き換え）モジュールを有効化
RUN cd /etc/apache2/mods-enabled \
  && ln -s ../mods-available/rewrite.load

#  カスタムPHP設定とApacheのバーチャルホスト設定を追加
ADD infra/docker/develop/php/php.dev.ini /usr/local/etc/php/

# 下記公式推奨
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
# Apache設定ファイル内のDocumentRootを置換
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/../!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

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

#  Node.js 20.x のインストール（最新のLTS対応）
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs

#  npm環境構築（フロントビルドツール用）
RUN rm -rf node_modules                              # node_modules削除（前段のゴミ削除）
RUN npm install -g npm n cross-env                   # npm自体 + cross-envなどツールをグローバルに
RUN npm cache clear --force                          # npmキャッシュ削除でクリーンな状態に
RUN npm install webpack --save                       # webpackインストール（ローカルプロジェクト想定）
RUN npm install node-sass --nodedir=/usr/include/node # node-sassビルド用にnodeヘッダ指定

# # Laravelプロジェクトのコピー（Laravel全体を /var/www/html に置く場合）
# COPY ./ /var/www/html
# # vendorだけは builder から持ってくる
COPY --from=builder /app var/www/thml
WORKDIR /var/www/html

RUN chown -R www-data:www-data storage
