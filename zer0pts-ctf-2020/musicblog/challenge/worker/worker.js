const fs = require('fs')
const md5 = require('md5');

const puppeteer = require('puppeteer');
const Redis = require('ioredis');
const connection = new Redis(6379, 'redis');

const admin_username = "admin";
const admin_password = "w28J0zjqpp6w9Ty8Sl58Z7iEf4h911zZ";
const flag = 'zer0pts{M4sh1m4fr3sh!!}';

const browser_option = {
    executablePath: 'google-chrome-unstable',
    headless: true,
    args: [
        '--no-sandbox',
        '--disable-background-networking',
        '--disable-default-apps',
        '--disable-extensions',
        '--disable-gpu',
        '--disable-sync',
        '--disable-translate',
        '--hide-scrollbars',
        '--metrics-recording-only',
        '--mute-audio',
        '--no-first-run',
        '--safebrowsing-disable-auto-update',
    ],
};
let browser = undefined;

const crawl = async (url) => {
    console.log(`[+] Query! (${url})`);
    const page = await browser.newPage();
    try {
        await page.setUserAgent(flag);
        await page.goto(url, {
            waitUntil: 'networkidle0',
            timeout: 3 * 1000,
        });
        page.click('#like');
        await page.waitForNavigation({timeout: 3000});
    } catch (err){
        console.log(err);
    }
    await page.close();
    console.log(`[+] Done! (${url})`)
};

const init = async () => {
    const browser = await puppeteer.launch(browser_option);
    const page = await browser.newPage();    
    console.log(`[+] Setting up...`);
    try {
        await page.goto(`http://challenge/login.php`);
        await page.waitFor('#username');
        await page.type('#username', admin_username);
        await page.waitFor('#password');
        await page.type('#password', admin_password);
        await page.waitFor('#login-submit');
        await Promise.all([
            page.$eval('#login-submit', elem => elem.click()),
            page.waitForNavigation()
        ]);
        const body = await page.evaluate(() => document.body.innerHTML);
        if (!body.includes('href="posts.php"')){
            throw Error(`Login failed at ${page.url()}.`);
        }
        console.log(`[+] Setup done!`);
    } catch (err) {
        console.log(`[-] Error while setting up :(`);
        console.log(err);
        const body = await page.evaluate(() => document.body.innerHTML);
        console.log(`body: ${body}`);
    }
    try{ 
        await page.close();
    } catch (err) {
        console.log(err);
    }
    return browser;
};

function handle(){
    console.log("[+] handle");
    connection.blpop("query", 0, async function(err, message) {
        if (browser === undefined) browser = await init();
        await crawl("http://challenge/post.php?id=" + message[1]);
        setTimeout(handle, 10); // handle next
    });
}
handle(); // first ignite
