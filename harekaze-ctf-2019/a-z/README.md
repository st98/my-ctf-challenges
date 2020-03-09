# [Misc 200] [a-z().]
## Description
`if (eval(your_code) === 1337) console.log(flag);`

`http://(redacted)` ([server/Dockerfile](server/Dockerfile))

- [a-z.tar.xz](attachments/a-z.tar.xz)

## Intended Solution
- `curl "http://(target)/?code=eval%28%28typeof%28this%29%29.constructor%28%28typeof%28this%29%29.length.constructor%28true%29%29.concat%28%28typeof%28this%29%29.big.name.length%29.concat%28%28typeof%28this%29%29.big.name.length%29.concat%28true.constructor.name.length%29%29"`

```
(typeof(this)).constructor => String
(typeof(this)).length.constructor => Number
(typeof(this)).length.constructor(true) => 1
(typeof(this)).big.name.length => 3
true.constructor.name.length => 7
```

## Flag
```
HarekazeCTF{sorry_about_last_year's_js_challenge...}
```