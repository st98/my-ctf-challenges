# zzz

`ssh -p22222 ctf@localhost` で接続後、`Ctrl + \` で `SIGQUIT` を送ればよい。  
これでフラグが送られてこないこともあるが、繰り返し試すといずれ成功する。

```
$ sshpass -p ctf ssh ctf@localhost -p 22222
^\/app/zzz.sh: line 2:   299 Quit                    sleep infinity
flag{eternal_spring_dream_27ff12ce}
Connection to localhost closed.
```
