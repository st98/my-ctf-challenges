<?php
require_once 'init.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  if (!is_string($username) || !is_string($password)) {
    $_SESSION['flush'] = 'Username and password must be string.';
    header('Location: /register.php');
    exit;
  }

  $pdo = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);

  try {
    $stmt = $pdo->prepare('SELECT username, is_admin FROM user WHERE username=? and password=?');
    $stmt->execute([$_POST['username'], sha256($_POST['password'])]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result === false) {
      $_SESSION['flush'] = 'Username or password is incorrect.';
      header('Location: /login.php');
      exit;
    }

    $_SESSION['username'] = (string)$result['username'];
    $_SESSION['is_admin'] = (int)$result['is_admin'];

    header('Location: /');
    exit;
  } catch (Exception $e) {
    
  }
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
          <h1 class="mt-4">Log in</h1>
<?php if (isset($flush)) { ?>
            <div class="alert alert-danger">
              <?= $flush ?>
            </div>
<?php } ?>
          <form action="/login.php" method="POST">
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" class="form-control" id="username" name="username">
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" id="login-submit" class="btn btn-primary">Log in</button>
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
