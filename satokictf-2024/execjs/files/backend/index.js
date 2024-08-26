import fs from 'node:fs/promises';

import express from 'express';
import { safeEval, escape } from './util.js';

const PORT = process.env.PORT || 3000;

const app = express();
app.use(express.urlencoded({ extended: false }));
app.use((req, res, next) => {
    res.setHeader('Content-Security-Policy', "default-src 'none'"); // 😉
    next();
});

const indexHtml = (await fs.readFile('index.html')).toString();
app.get('/', async (req, res) => {
    res.setHeader('Content-Type', 'text/html');
    return res.send(indexHtml.replaceAll('{OUTPUT}', ''));
});
app.post('/', async (req, res) => {
    let result = '';
    const { code } = req.body;

    if (code && typeof code === 'string') {
        try {
            result = (await safeEval(code)).toString();
        } catch {
            result = 'An error occurred.';
        }
    }

    const html = indexHtml.replaceAll('{OUTPUT}', escape(result));
    res.setHeader('Content-Type', 'text/html');
    return res.send(html);
});

app.listen(PORT, () => {
    console.log(`listening on port ${PORT}`);
});