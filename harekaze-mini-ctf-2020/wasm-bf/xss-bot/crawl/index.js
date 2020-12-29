const puppeteer = require('puppeteer');

module.exports = async function (context, req) {
    const url = process.env['BASE_URI'] + req.query.url;
    context.log.info(`requested URL: ${url}`);

    try {
        const browser = await puppeteer.launch();
        const page = await browser.newPage();
        await page.setCookie({
            name: 'flag',
            value: process.env['FLAG'],
            domain: process.env['CHALLENGE_DOMAIN'],
            httpOnly: false,
            secure: false
        });
        
        await page.goto(url, {
            waitUntil: 'domcontentloaded',
            timeout: 3000
        });
        await page.waitForTimeout(3000);
        await page.close();
        await browser.close();

        context.res = {
            body: '{"status":"ok"}',
            headers: {
                "content-type": "application/json"
            }
        };
    } catch (e) {
        context.log.error(e);
        context.res = {
            body: '{"status":"error"}',
            headers: {
                "content-type": "application/json"
            }
        }
    }
}