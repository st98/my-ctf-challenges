<?php
function redirect($path) {
  header('Location: ' . $path);
  exit();
}

function flash($message, $path = '?page=index') {
  $_SESSION['flash'] = $message;
  redirect($path);
}

function e($string) {
  return htmlspecialchars($string, ENT_QUOTES);
}

function is_valid($string) {
  $banword = [
    // comment out, calling function...
    "[\"#'()*,\\/\\\\`-]"
  ];
  $regexp = '/' . implode('|', $banword) . '/i';
  if (preg_match($regexp, $string)) {
    return false;
  }
  return true;
}