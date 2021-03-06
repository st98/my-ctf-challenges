<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log in - <?= CHALLENGE_NAME; ?></title>
</head>
<body>
  <h1 class="title">
    Welcome to <?= CHALLENGE_NAME; ?>!
  </h1>
  <ul>
    <li><a href="/?page=home">Home</a></li>
<?php if ($session->has('username')) { ?>
    <li><a href="/?page=logout">Log out</a></li>
<?php if ($session->get('role') === 'admin') { ?>
    <li><a href="/?page=admin">Admin</a></li>
<?php } else { ?>
    <li><s>Admin</s></li>
<?php } ?>
<?php } else { ?>
    <li><a href="/?page=login">Log in</a></li>
<?php } ?>
  </ul>
  <form action="/?page=login" method="POST">
    <input type="text" name="username" placeholder="username">
    <input type="submit" value="Log in">
  </form>
</body>
</html>