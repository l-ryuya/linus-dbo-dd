# 起動手順
Docker Desktopがインストールされていることを前提としています。

リポジトリのルートから下記のコマンドにて起動。
```shell
$ cd infra/docker/m5
$ docker compose up -d
```

# 初期データ
`infra/docker/m5/docker-db/m5/dev` 以下にSQLファイルが配置されており、コンテナ起動時に自動で実行されます。

# エンドポイント
[m5-api Rest-API Document](https://crispy-guacamole-14f9f8b5.pages.github.io/)

## APIエンドポイント
- 認証
  - http://localhost:9100/m5/v1/authentication/sign-in
- ユーザ一覧
  - http://localhost:9101/m5/v2-0/users

その他のAPIエンドポイントURLはドキュメントを参照。