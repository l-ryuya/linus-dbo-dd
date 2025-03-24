# bizdevforge API
- PHP 8.3.x
- Laravel 12.x
- [PER Coding Style 2.0](https://www.php-fig.org/per/coding-style/)

# 要件
## 多言語化
- 日本語
- 英語

## タイムゾーン  
日本に在住するユーザーを対象とするため動的には切り替えない  

# 仕様
## リクエスト HTTPヘッダ
- 指定しないとレスポンスがJSONにならない  
`Accept: application/json`

- 言語切替  
`Accept-Language: ja`　日本語  
`Accept-Language: en`　英語

- アクセストークン  
`Authorization: Bearer <発行されたトークン>`

# 開発者向けのリンク集
[Gitの利用方法とCIのセットアップガイド](https://esq365.sharepoint.com/sites/isid-scm/SitePages/%E3%82%BD%E3%83%95%E3%83%88%E3%82%A6%E3%82%A7%E3%82%A2%E9%96%8B%E7%99%BA%E3%82%92%E5%88%9D%E3%82%81%E3%82%8B%E5%89%8D%E3%81%AB.aspx)
