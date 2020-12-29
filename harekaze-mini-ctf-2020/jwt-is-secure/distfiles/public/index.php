<?php
include 'config.php';
include 'session.php';

$session = new Session(COOKIE_NAME, './keys');
$page = (string)$_GET['page'] ?: 'home';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (in_array($page, ['home', 'login', 'admin'], TRUE)) {
    include 'pages/' . $page . '.php';
  }

  if ($page === 'logout') {
    setcookie(COOKIE_NAME, '', time() - 1000);
    header('Location: /?page=home');
  }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($page === 'login') {
    $username = (string)$_POST['username'];

    if ($username === 'admin') {
      header('Location: /?page=home');
      exit;
    }

    $session->set('username', $username);
    $session->set('role', 'user');
    header('Location: /?page=home');
  }
}