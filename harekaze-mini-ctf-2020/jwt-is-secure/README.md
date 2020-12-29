# [Web] JWT is secure
## Description
私は独自に作ったセッション機能は脆弱性を作り込みがちだということを学んだので、今回はJWT (JSON Web Token)を採用しました。

---

I learned implementing a custom session function is prone to be insecure, so this time I adopted JWT (JSON Web Token).

(URL)

## Attachments
- [distfiles/](distfiles/)

## Intended solution
- [Japanese](https://st98.github.io/diary/posts/2020-12-29-harekaze-mini-ctf-2020.html#web-210-jwt-is-secure-19-solves)

## Writeups
- (ja) [TanさんはTwitterを使っています 「#HarekazeCTF お疲れさまでした。今回はWeb問担当でした。 What time is it now?: escapeshellcmdでは対になるクォートは残るので「--file=/flag' '--file=/flag」を投げ込む(引数が1つだと何故か出なかった) JWT is secure: roleをadmin、kidを「/.htaccess」にして、配布ファイルにあるそれで署名。」 / Twitter](https://twitter.com/Tan90909090/status/1343010507402297345)
- (ja) [Harekaze mini CTF 2020 writeup - Qiita](https://qiita.com/mikecat_mixc/items/ff061c54baae558f9058#jwt-is-secure)
- (ja) [Harekaze mini CTF 2020 writeup for web challs](https://blog.arkark.dev/2020/12/27/harekaze-ctf/#web-JWT-is-secure)
- (ja) [Harekaze mini CTF 2020 write-up - Qiita](https://qiita.com/kusano_k/items/de8947497630383ddf2a#jwt-is-secure-easy)

## Flag
```
HarekazeCTF{l1st3n_1_just_g1v3_y0u_my_fl4g_4t4sh1_n0_w4v3_w0_t0b4sh1t3_m1ruk4r4}
```