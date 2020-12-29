# メモ
## 環境変数の設定
- `BASE_URI`
  - これに `?hoge#fuga` みたいなパラメータをくっつけてChromiumがアクセスしに行く
  - `http://example.com/` とかに設定しておく
- `CHALLENGE_DOMAIN`
  - これを `domain` オプションとしてフラグがCookieに設定される
  - `example.com` とかに設定しておく
- `FLAG`
  - これがCookieの設定値になる
  - `flag{DUMMY}` とかに設定しておく