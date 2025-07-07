# # builder ステージ（composer:ltsイメージを使用）
FROM composer:lts AS builder

COPY ./ /app/
WORKDIR /app

# Composerで依存インストール（本番用にno-devオプション）
RUN composer install -q --no-dev --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist && \
    composer dump-autoload

FROM php:8.4-apache

RUN a2enmod rewrite

# 下記公式の記述方法
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
# Apache設定ファイル内のDocumentRootを置換
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/../!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
COPY infra/docker/develop/php/php.dev.ini /usr/local/etc/php/

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

# # Laravelプロジェクトのコピー（Laravel全体を /var/www/html に置く場合）
COPY --from=builder /app /var/www/html
WORKDIR /var/www/html

RUN chown -R www-data:www-data storage
