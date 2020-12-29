<?php
function hs256($data, $key) {
  return hash_hmac('sha256', $data, $key, TRUE);
}

function hs384($data, $key) {
  return hash_hmac('sha384', $data, $key, TRUE);
}

function hs512($data, $key) {
  return hash_hmac('sha512', $data, $key, TRUE);
}

// http://php.net/manual/en/function.base64-encode.php#121767
function urlsafe_base64_encode($data) {
  return rtrim(str_replace(['+', '/'], ['-', '_'], base64_encode($data)), '=');
}

function urlsafe_base64_decode($data) {
  return base64_decode(str_replace(['-', '_'], ['+', '/'], $data) . str_repeat('=', 3 - (3 + strlen($data)) % 4));
}