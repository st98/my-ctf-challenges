<?php
error_reporting(0);

require_once 'config.php';
require_once 'util.php';

$nonce = get_nonce();
header("Content-Security-Policy: default-src 'self'; object-src 'none'; script-src 'nonce-${nonce}' 'strict-dynamic'; base-uri 'none'; trusted-types");
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

session_start();