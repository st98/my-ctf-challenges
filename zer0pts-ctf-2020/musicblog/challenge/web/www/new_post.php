<?php
require_once 'init.php';

if (!isset($_SESSION['username'])) {
  $_SESSION['flush'] = 'You need to log in to access this page.';
  header('Location: /login.php');
  exit;
}

if (isset($_POST['title']) && isset($_POST['content'])) {
  $title = $_POST['title'];
  $content = $_POST['content'];
  $publish = (int)isset($_POST['publish']);

  if (!is_string($title) || !is_string($content)) {
    $_SESSION['flush'] = 'Title and content must be string.';
    header('Location: /new_post.php');
    exit;
  }

  if (!preg_match('/^[0-9A-Za-z ]+$/', $title)) {
    $_SESSION['flush'] = 'Title must be /^[0-9A-Za-z ]+$/.';
    header('Location: /new_post.php');
    exit;
  }

  $id = uuid4();
  $pdo = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);

  try {
    $stmt = $pdo->prepare('INSERT INTO post (id, username, title, content, published) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$id, $_SESSION['username'], $title, $content, $publish]);
  } catch (Exception $e) {

  }

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

  header('Location: /posts.php');
  exit;
}

if (isset($_SESSION['flush'])) {
  $flush = (string)$_SESSION['flush'];
  unset($_SESSION['flush']);
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MusicBlog</title>
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/css/style.css">
  </head>
  <body>
    <header>
      <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
          <a class="navbar-brand" href="/">MusicBlog</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarDropdown" aria-controls="navbarDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="">︙</span>
          </button>
          <div class="collapse navbar-collapse" id="navbarDropdown">
            <ul class="navbar-nav mr-auto">
              <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
<?php if (isset($_SESSION['username'])) { ?>
              <li class="nav-item"><a class="nav-link" href="posts.php">Your posts</a></li>
<?php } ?>
            </ul>
            <ul class="navbar-nav">
<?php if (isset($_SESSION['username'])) { ?>
              <li class="nav-item"><a class="nav-link" href="new_post.php">New post</a></li>
              <li class="nav-item"><a class="nav-link" href="logout.php">Log out</a></li>
<?php } else { ?>
              <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
              <li class="nav-item"><a class="nav-link" href="login.php">Log in</a></li>
<?php } ?>
            </ul>
          </div>
        </div>
      </nav>
    </header>
    <main>
      <section>
        <div class="container">
          <h1 class="mt-4">New post</h1>
<?php if (isset($flush)) { ?>
            <div class="alert alert-danger">
              <?= $flush ?>
            </div>
<?php } ?>
          <form action="/new_post.php" method="POST">
            <div class="form-group">
              <label for="title">Title</label>
              <input type="text" class="form-control" id="title" name="title">
              <small class="form-text text-muted">format: <code>/^[0-9A-Za-z ]+$/</code></small>
            </div>
            <div class="form-group">
              <label for="content">Content</label>
              <textarea class="form-control" id="content" name="content" rows="5"></textarea>
              <small class="form-text text-muted">Note: <code>[[URL]]</code> will be replaced by audio player.</small>
            </div>
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="publish" name="publish">
              <label for="publish">Publish this post (If you check this checkbox, admin will check your post.)</label>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>
        </div>
      </section>
    </main>
    <footer>
      <div class="container text-center">
        <span class="text-muted">&copy; 2020- <a href="https://ctftime.org/team/54599">zer0pts</a></span>
      </div>
    </footer>
    <script src="/static/js/jquery-3.4.1.min.js" nonce="<?= $nonce ?>"></script>
    <script src="/static/js/bootstrap.bundle.min.js" nonce="<?= $nonce ?>"></script>
  </body>
</html>