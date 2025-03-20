# 起動手順
Docker Desktopがインストールされている想定。  
リポジトリのルートから下記のコマンドにて起動。
```shell
$ cd infra/docker/local
$ docker compose up -d
```
起動するまでに時間が掛かります。

http://localhost/　にアクセスして404が返ってくれば起動しています。
```JSON
{"statusCode":404,"message":"The route \/ could not be found."}
```

データベースにテーブルを作成します。
```shell
$ docker exec -it local-bizdevforge-api-1 php artisan migrate
```
