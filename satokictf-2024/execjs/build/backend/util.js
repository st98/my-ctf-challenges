import child_process from 'node:child_process';
import fs from 'node:fs/promises';

import tmp from 'tmp';

export async function safeEval(code) {
    let result = null;

    const { name: tmpdir, removeCallback } = tmp.dirSync();
    const txtpath = `${tmpdir}/sample.txt`;
    const jspath = `${tmpdir}/index.js`;

    try {
        await fs.writeFile(txtpath, 'hello');
        await fs.writeFile(jspath, code);

        const proc = child_process.execFileSync('node', [
            '--experimental-permission',
            `--allow-fs-read=${tmpdir}`,
            '--noexpose_wasm',
            '--jitless',
            jspath
        ], {
            timeout: 60_000,
            cwd: tmpdir,
            stdio: ['ignore', 'pipe', 'pipe']
        });
        result = proc;
    } catch(e) {
        console.error('[err]', e);
    } finally {
        await fs.unlink(txtpath);
        await fs.unlink(jspath);
        removeCallback();
    }

    return result;
};

const escapeTable = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;'
};
export function escape(html) {
    return html.replaceAll(/[&<>"']/g, s => {
        return escapeTable[s];
    })
};