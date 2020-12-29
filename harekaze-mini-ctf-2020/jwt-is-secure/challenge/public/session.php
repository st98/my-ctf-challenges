<?php
include 'jwt.php';

class Session {
  private $cookie_name;
  private $jwt;
  private $key;
  private $base_dir;
  
  public function __construct($cookie_name='jwtsession', $dir='./keys') {
    $this->cookie_name = $cookie_name;
    $this->base_dir = $dir;

    if (array_key_exists($cookie_name, $_COOKIE)) {
      try {
        $tmp = new JWT($_COOKIE[$cookie_name]);
        $kid = $tmp->getHeader('kid');
        $this->key = $this->getSecretKey($kid);

        if (!$tmp->verify($this->key)) {
          throw new Exception('Signature verification failed');
        }

        $this->jwt = $tmp;
      } catch (Exception $e) {
        die('Error occurred: ' . $e->getMessage());
      }
    }

    if (!isset($this->jwt)) {
      $this->jwt = new JWT;
      $kid = bin2hex(random_bytes(8));
      $this->key = bin2hex(random_bytes(64));
      $this->setSecretKey($kid, $this->key);
      $this->jwt->setHeader('kid', $kid);
      $this->save();
    }
  }

  private function getSecretKey($kid) {
    $dir = $this->base_dir . '/' . $kid[0] . '/' . $kid[1];
    $path = $dir . '/' . $kid;

    // no path traversal, no stream wrapper
    if (preg_match('/\.\.|\/\/|:/', $kid)) {
      throw new Exception('Hacking attempt detected');
    }

    if (!file_exists($path) || !is_file($path)) {
      throw new Exception('Secret key not found');
    }

    return file_get_contents($path);
  }

  private function setSecretKey($kid, $key) {
    $dir = $this->base_dir . '/' . $kid[0] . '/' . $kid[1];
    $path = $dir . '/' . $kid;

    if (!file_exists($dir)) {
      mkdir($dir, 0777, TRUE);
    }

    file_put_contents($path, $key);
  }

  public function has($key) {
    return $this->jwt->hasData($key);
  }

  public function get($key) {
    return $this->jwt->getData($key);
  }

  public function set($key, $value) {
    $this->jwt->setData($key, $value);
    $this->save();
  }

  private function save() {
    setcookie($this->cookie_name, $this->jwt->sign($this->key));
  }
}