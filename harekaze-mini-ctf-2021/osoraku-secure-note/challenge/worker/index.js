const puppeteer = require('puppeteer');
const Redis = require("ioredis");

const FLAG = process.env.FLAG || 'HarekazeCTF{DUMMY}';
const BASE_URL = process.env.BASE_URL || "http://challenge:3000";
const browser_option = {
  executablePath: '/usr/bin/google-chrome',
  headless: true,
  args: [
    '--no-sandbox',
    '--disable-dev-shm-usage',
    '--disable-gpu',
    '--js-flags="--noexpose_wasm"'
  ]
};

const REDIS_URL = process.env.REDIS_URL || 'redis://127.0.0.1:6379';
const redis = new Redis(REDIS_URL);

const crawl = async (id) => {
  const url = BASE_URL + '/note/' + id;
  console.log(`[+] Crawling started: ${url}`);

  const browser = await puppeteer.launch(browser_option);

  const page = await browser.newPage();
  try {
    await page.setCookie({
      name: 'flag',
      value: FLAG,
      domain: new URL(BASE_URL).hostname,
      httpOnly: false,
      secure: false
    });
    await page.goto(url, {
      waitUntil: 'networkidle0',
      timeout: 3 * 1000,
    });
  } catch (e) {
    console.log('[-]', e);
  } finally {
    await page.close();
  }

  await browser.close();
};

const handle = () => {
  redis.blpop('query', 0, async (err, message) => {
    try {
      await crawl(message[1]);
    } catch (e) {
      console.log('[-]', e);
    }
    setTimeout(handle, 10);
  });
};

handle();
