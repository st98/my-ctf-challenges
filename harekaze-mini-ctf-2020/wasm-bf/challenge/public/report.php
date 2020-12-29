<?php
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

  $resp2 = file_get_contents('https://(snipped)?&url=' . urlencode($_POST['param']));
  $resp2 = json_decode($resp2, true);
  if ($resp2['status'] === 'ok') {
    die('ok');
  } else {
    die('failed');
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Report Vulnerability</title>
</head>
<body>
  <h1>Report Vulnerability</h1>
  <p>If you find a vulnerability, please report a Proof of Concept code that exfiltrates <code>document.cookie</code> to me. I will check the URL with Chromium later.</p>
  <p>I only accept reports under <code>/index.html</code>, so please submit only <code>location.search</code> and <code>location.hash</code> part of a URL. For example, if the URL you want to submit is <code>http://example.com/index.php?hoge#----[----&gt;+&lt;]&gt;++.</code>, please submit only <code>?hoge#----[----&gt;+&lt;]&gt;++.</code> part.</p>
  <form action="/report.php" method="post" id="form">
    <input type="text" name="param" placeholder="e.g. ?hoge#----[---->+<]>++">
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
