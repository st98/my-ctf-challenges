<?php
function sha256($s) {
  return hash('sha256', $s);
}

function update_nonce() {
  $_SESSION['nonce'] = substr(md5('7h15_15_5417!' . mt_rand(0, 0xffffffff)), 0, 16);
  return $_SESSION['nonce'];
}

session_start();

header('Content-Type: application/json');

if (!isset($_POST['username']) || !isset($_POST['hash']) || !isset($_POST['cnonce'])) {
  die('{"error":"please enter username and password","nonce":"' . update_nonce() . '"}');
}

$nonce = $_SESSION['nonce'];
$cnonce = $_POST['cnonce'];
$username = $_POST['username'];
$hash = $_POST['hash'];

$correct_hash = sha256(sha256('HarekazeCTF{7r1663r_h4ppy_61rl}') .':' . $nonce . ':' . $cnonce);
if ($username === 'irizaki_mei' && hash_equals($hash, $correct_hash)) {
  $_SESSION['username'] = $username;
  die('{"message":"login succeeded"}');
} else {
  die('{"error":"incorrect username or password","nonce":"' . update_nonce() . '"}');
}