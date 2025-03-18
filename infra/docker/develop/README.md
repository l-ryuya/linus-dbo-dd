# 課題と不明点

## vendorが存在してないとコンテナ起動時にエラーになる
コンテナビルド時にソースを仮環境に置いてcompser installしてからコピーする？

composerがインストールされている環境で下記を実行する
```shell
$ composer install --no-dev
```

## commandを実行するとコンテナが起動しない
commandを実行するとentrypointが上書きされてしまうらしい。  
commandが終了するとforegroundのプロセスがいなくなりコンテナも終了してしまうそう。

```shell
$ php artisan migrate
$ php artisan db:seed
```
migrateは都度、起動しても可
db:seedは初回のみ、または都度
