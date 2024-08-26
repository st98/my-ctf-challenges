import { chromium } from 'playwright';
import express from 'express';
import jwt from 'jsonwebtoken';

const PORT = process.env.PORT || 3000;
const FLAG = process.env.FLAG || 'flag{dummy}';

const RECAPTCHA_SITE_KEY = process.env.RECAPTCHA_SITE_KEY;
const RECAPTCHA_SECRET_KEY = process.env.RECAPTCHA_SECRET_KEY;
const RECAPTCHA_ENABLED = RECAPTCHA_SITE_KEY && RECAPTCHA_SECRET_KEY;

const SECRET_KEY = process.env.SECRET_KEY || 'what';

function sleep(t) {
    return new Promise(r => setTimeout(r, t));
}

const visit = async site => {
    console.log('visiting: ' + site);

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

        await page.goto(site, { timeout: 3000, waitUntil: 'networkidle' });
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
app.use(express.urlencoded({ extended: false }));

app.get('/crawl', async (req, res) => {
    const { target, token } = req.query;

    if (RECAPTCHA_ENABLED) {
        if (token == null || typeof token != 'string') {
            return res.send('nope');
        }

        try {
            const resp = await fetch('https://www.google.com/recaptcha/api/siteverify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `secret=${RECAPTCHA_SECRET_KEY}&response=${token}`
            });
            const result = await resp.json();
            if (!result.success) {
                return res.send('nope');
            }
        } catch (e) {
            console.log(e);
            return res.send('nope');
        }
    }

    if (target == null || typeof target != 'string') {
        return res.send('nope');
    }

    let claims;
    try {
        claims = jwt.verify(target, SECRET_KEY);
    } catch {
        return res.send('nope');
    }

    if (!(claims.url.startsWith('http://') || claims.url.startsWith('https://'))) {
        return res.send('wtf hacker');
    }

    visit(claims.url).then(r => {
        return res.send('done');
    }).catch(r => {
        return res.send('something wrong');
    });
});

app.listen(PORT, () => {
    console.log(`listening on port ${PORT}`);
});