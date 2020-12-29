<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home - <?= CHALLENGE_NAME; ?></title>
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
  <p>
<?php if ($session->has('username')) { ?>
    Hello, <b><?= $session->get('username') ?></b>! Your role is <b><?= $session->get('role') ?></b>, so you <b><?= $session->get('role') === 'admin' ? 'can' : 'cannot' ?></b> access <a href="/?page=admin">admin page</a>.
<?php } else { ?>
    You need to <a href="/?page=login">log in</a> to access this page!
<?php } ?>
  </p>
</body>
</html>