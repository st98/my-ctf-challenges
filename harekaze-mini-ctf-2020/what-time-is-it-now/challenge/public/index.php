<?php
if (isset($_GET['source'])) {
  highlight_file(__FILE__);
  exit;
}

$format = isset($_REQUEST['format']) ? (string)$_REQUEST['format'] : '%H:%M:%S';
$result = shell_exec("date '+" . escapeshellcmd($format) . "' 2>&1");
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>What time is it now?</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  </head>
  <body>
   <header>
      <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
          <a class="navbar-brand" href="/">What time is it now?</a>
          <div class="navbar-collapse">
            <ul class="navbar-nav mr-auto">
              <li class="nav-item"><a class="nav-link" href="?source">Source Code</a></li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
    <main>
      <section class="jumbotron text-center">
        <div class="container">
          <h1 class="jumbotron-heading"><span class="text-muted">It's</span> <?= isset($result) ? $result : '?' ?><span class="text-muted">.</span></h1>
          <p>
            <a href="?format=%H:%M:%S" class="btn btn-outline-secondary">What time is it now?</a>
            <a href="?format=%Y-%m-%d" class="btn btn-outline-secondary">What is the date today?</a>
            <a href="?format=%s" class="btn btn-outline-secondary">What time is it now in UNIX time?</a>
          </p>
        </div>
      </section>
    </main>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>