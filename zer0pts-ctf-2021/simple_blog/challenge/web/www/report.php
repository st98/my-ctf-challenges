<?php
try {
  if (isset($_POST['g-recaptcha-response']) && isset($_POST['param'])) {
    $data = 'secret=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX&response=' . urlencode($_POST['g-recaptcha-response']);
    $headers = [
      'Content-Type: application/x-www-form-urlencoded',
      'Content-Length: ' . strlen($data)
    ];
    $resp = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, stream_context_create([
      'http' => [
        'method' => 'POST',
        'header' => implode("\r\n", $headers),
        'content' => $data
      ]
    ]));

    $resp = json_decode($resp, true);
    if (!$resp['success']) {
      die('reCAPTCHA failed');
    }

    $redis = new Redis();
    $redis->connect("redis", 6379);
    $redis->rPush("query", $_POST['param']);
  }
} catch (Exception $e) {
  print($e);
  exit(0);
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Report Vulnerability</title>
  </head>
  <body>
    <h1>Report Vulnerability</h1>
    <p>If you find a vulnerability in my blog platform, please report a Proof of Concept that exfiltrates <code>document.cookie</code> to me. I will check the URL with Mozilla Firefox later.</p>
    <p>I only accept reports under <code>/index.php</code>, so please submit only GET paremeters. For example, if the URL you want to submit is <code>http://example.com/index.php?theme=light</code>, please submit only <code>theme=light</code> part.</p>
    <form action="/report.php" method="post" id="form">
      <input type="text" name="param" placeholder="e.g. theme=light">
      <button class="g-recaptcha" 
        data-sitekey="XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX" 
        data-callback='onSubmit' 
        data-action='submit'>Report</button>
    </form>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script>
    function onSubmit(token) {
      document.getElementById('form').submit();
    }
    </script>
  </body>
</html>