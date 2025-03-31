# bizdevforge API
- PHP 8.3.x
- Laravel 12.x
- [PER Coding Style 2.0](https://www.php-fig.org/per/coding-style/)

# 要件
## 多言語化
- 英語
- 日本語
- ＋6カ国語を予定

## タイムゾーン  
プロトタイプでは動的に切り替えない。

# 仕様
## リクエスト HTTPヘッダ
- 指定しないとレスポンスがJSONにならない  
`Accept: application/json`

- 言語切替  
`Accept-Language: eng`　英語  
`Accept-Language: jpn`　日本語

- アクセストークンの指定  
`Authorization: Bearer <発行されたトークン>`

## 認証
プロトタイプでのみ利用する仮実装。  
ログイン、ログアウトを提供しアクセストークンを返す。  
リフレッシュトークンなどは提供しない。
CSRFトークンなども同様に提供しない。

# 開発者向けのリンク集
[Gitの利用方法とCIのセットアップガイド](https://esq365.sharepoint.com/sites/isid-scm/SitePages/%E3%82%BD%E3%83%95%E3%83%88%E3%82%A6%E3%82%A7%E3%82%A2%E9%96%8B%E7%99%BA%E3%82%92%E5%88%9D%E3%82%81%E3%82%8B%E5%89%8D%E3%81%AB.aspx)
