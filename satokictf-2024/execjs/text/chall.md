## EXECjs

[TsukuCTF 2023のEXECpy](https://github.com/satoki/tsukuctf_2023_satoki_writeups/tree/c4e07286dfc0e74d923f37666aa03882d7338735/web/EXECpy)をパクってNode.js版を作りました。RCE2XSSしてください。

InstancerのURL

※ローカルでflagが取得できることを確認した後にリモートで試してください。  
※添付ファイル中のcrawlerは、ローカルで試しやすいよう改変を加えたものです。本物の問題サーバでは、`visit` 関数を除いて大きく異なるコードが動いていますので、ご注意ください。