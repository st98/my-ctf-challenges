<?php
require_once 'init.php';

if (!isset($_GET['id'])) {
  header('Location: /posts.php');
  exit;
}

$id = (string)$_GET['id'];

$pdo = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);

try {
  $stmt = $pdo->prepare('SELECT id, username, title, content, likes, published FROM post WHERE id=?');
  $stmt = $pdo->prepare('UPDATE post SET likes=likes+1 WHERE id=?');
  $stmt->execute([$id]);
} catch (Exception $e) {

}

header("Location: /post.php?id=${id}");
exit;
