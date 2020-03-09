<?php
require_once 'init.php';

if (!isset($_SESSION['username'])) {
  $_SESSION['flush'] = 'You need to log in to access this page.';
  header('Location: /login.php');
  exit;
}

$pdo = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
$posts = [];

try {
  $stmt = $pdo->prepare('SELECT id, username, title, likes, published FROM post WHERE username=?');
  $stmt->execute([$_SESSION['username']]);
  $posts = array_reverse($stmt->fetchall(PDO::FETCH_ASSOC));
} catch (Exception $e) {
  
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
          <h1 class="mt-4">Your posts</h1>
<?php if (count($posts) === 0) { ?>
          <p>No posts.</p>
<?php } else { ?>
          <ul class="list-group">
<?php for ($i = 0; $i < count($posts); $i++) { ?>
            <li class="list-group-item">
              <?php if ($posts[$i]['published'] === '0') { ?><span class="badge badge-secondary">Secret</span><?php } ?>
              <a href="/post.php?id=<?= $posts[$i]['id'] ?>"><?= $posts[$i]['title'] ?></a> by <?= $posts[$i]['username'] ?>
              <span class="badge badge-love badge-pill">♥ <?= $posts[$i]['likes'] ?></span>
            </li>
<?php } ?>
          </ul>
<?php } ?>
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