# 起動手順
Docker Desktopがインストールされている想定  

リポジトリのルートから下記のコマンドにて起動
```shell
$ cd infra/docker/local
$ docker compose up -d
```

コンテナにログインする
```shell
docker exec -it local-bizdevforge-api-1 /bin/bash
```

## 環境設定

### composerを実行
```shell
$ composer install --no-dev
```
開発環境なら --no-dev は不要

### .env による環境変数を設定
.env.example をコピーして .env を作成する

infra/docker/local/compose.yml の environment 
に記述されている環境変数が有効となる。  
場合によっては.envにある同名の環境変数は削除する。

### データベースにテーブルを作成
```shell
$ php artisan migrate
```

### 初期データの登録
```shell
$ php artisan db:seed
```

### 起動確認
http://localhost/　にアクセスして404が返ってくれば起動しています。
```JSON
{"statusCode":404,"message":"The route \/ could not be found."}
```
