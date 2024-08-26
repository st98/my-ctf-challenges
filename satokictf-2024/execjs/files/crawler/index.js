import { chromium } from 'playwright';
import express from 'express';

const PORT = process.env.PORT || 3000;
const FLAG = process.env.FLAG || 'flag{dummy}';
const SITE = 'http://backend:3000'; // note: this will be replaced to the URL of your own instance

function sleep(t) {
    return new Promise(r => setTimeout(r, t));
}

const visit = async () => {
    console.log('visiting');

    let browser;
    try {
        browser = await chromium.launch({
            executablePath: '/usr/bin/chromium',
            headless: true,
            pipe: true,
            args: [
                '--disable-dev-shm-usage',
                '--disable-gpu',
                '--js-flags=--noexpose_wasm,--jitless',
            ],
            dumpio: true
        });

        const context = await browser.newContext();
        const page = await context.newPage();

        await page.goto(SITE, { timeout: 3000, waitUntil: 'networkidle' });
        page.evaluate(flag => {
            localStorage.flag = flag;
        }, FLAG);
        await sleep(5000);

        await browser.close();
        browser = null;
    } catch (e) {
        console.log(e);
    } finally {
        if (browser) await browser.close();
    }

    console.log('done');
};

const app = express();

app.get('/', (req, res) => {
    visit();
    return res.send('crawling');
});

app.listen(PORT, () => {
    console.log(`listening on port ${PORT}`);
});