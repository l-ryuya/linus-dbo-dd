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
## OpenAPI仕様書
api-docsを参照

## 認証
プロトタイプでのみ利用する仮実装。  
ログイン、ログアウトを提供しアクセストークンを返す。  
リフレッシュトークンなどは提供しない。
CSRFトークンなども同様に提供しない。
