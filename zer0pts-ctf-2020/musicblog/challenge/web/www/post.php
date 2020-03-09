<?php
require_once 'init.php';

if (isset($_GET['id'])) {
  $id = (string)$_GET['id'];

  $pdo = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);

  try {
    $stmt = $pdo->prepare('SELECT id, username, title, content, likes, published FROM post WHERE id=?');
    $stmt->execute([$id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post === false) {
      header('Location: /posts.php');
      exit;
    }
  } catch (Exception $e) {
    
  }
} else {
  header('Location: /posts.php');
  exit;
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
          <h1 class="mt-4">
            <?php if ($post['published'] === '0') { ?><span class="badge badge-secondary">Secret</span><?php } ?>
            <?= $post['title'] ?>
          </h1>
          <span class="text-muted">by <?= $post['username'] ?> <span class="badge badge-love badge-pill">♥ <?= $post['likes'] ?></span></span>
          <div class="mt-3">
            <?= render_tags($post['content']) ?>
          </div>
          <div class="mt-3">
            <a href="like.php?id=<?= $post['id'] ?>" id="like" class="btn btn-love">♥ Like this post</a>
          </div>
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