<?php
include 'util.php';

class JWT {
  private $string_header, $header;
  private $string_data, $data;
  private $signature;

  function __construct($jwt=NULL) {
    if (isset($jwt)) {
      list($header, $data, $signature) = explode('.', $jwt);
      $this->string_header = $header;
      $this->header = json_decode(urlsafe_base64_decode($this->string_header), TRUE);
      $this->string_data = $data;
      $this->data = json_decode(urlsafe_base64_decode($this->string_data), TRUE);
      $this->signature = urlsafe_base64_decode($signature);
    } else {
      $this->header = ['typ' => 'JWT'];
      $this->string_header = urlsafe_base64_encode(json_encode($this->header, JSON_FORCE_OBJECT));
      $this->data = [];
      $this->string_data = urlsafe_base64_encode(json_encode($this->data, JSON_FORCE_OBJECT));
    }
  }

  function hasHeader($key) {
    return array_key_exists($key, $this->header);
  }

  function hasData($key) {
    return array_key_exists($key, $this->data);
  }

  function getHeader($key) {
    return $this->header[$key];
  }

  function getData($key) {
    return $this->data[$key];
  }

  function setHeader($key, $value) {
    $this->header[$key] = $value;
    $this->string_header = urlsafe_base64_encode(json_encode($this->header, JSON_FORCE_OBJECT));
  }

  function setData($key, $value) {
    $this->data[$key] = $value;
    $this->string_data = urlsafe_base64_encode(json_encode($this->data, JSON_FORCE_OBJECT));
  }

  function verify($key) {
    $algo = strtolower($this->header['alg']);
    if (!in_array($algo, ['hs256', 'hs384', 'hs512'], TRUE)) {
      return FALSE;
    }

    $signing_input = $this->string_header . '.' . $this->string_data;
    $real_signature = $algo($signing_input, $key);
    if (!hash_equals($real_signature, $this->signature)) {
      return FALSE;
    }

    return TRUE;
  }

  function sign($algo, $key=NULL) {
    if (!isset($key) || empty($key)) {
      $key = $algo;
      $algo = 'HS256';
    }

    $this->setHeader('alg', $algo);
    $algo = strtolower($this->getHeader('alg'));
    if (!in_array($algo, ['hs256', 'hs384', 'hs512'], TRUE)) {
      return NULL;
    }

    $signing_input = $this->string_header . '.' . $this->string_data;
    $this->signature = $algo($signing_input, $key);

    return $signing_input . '.' . urlsafe_base64_encode($this->signature);
  }
}