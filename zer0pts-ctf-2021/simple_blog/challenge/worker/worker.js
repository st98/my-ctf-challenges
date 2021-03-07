const puppeteer = require('puppeteer');
const Redis = require('ioredis');
const connection = new Redis(6379, 'redis');

const flag = 'zer0pts{1_w4nt_t0_e4t_d0m_d0m_h4mburger_s0med4y}';
const browser_option = {
  headless: true,
  args: [
    '-wait-for-browser'
  ]
}

let browser = undefined;

const init = async () => {
  const browser = await puppeteer.launch(browser_option);
  return browser;
};

const crawl = async (url) => {
  console.log(`[+] Crawling started: ${url}`);

  try {
    const page = await browser.newPage();
    await page.setCookie({
      name: 'flag',
      value: flag,
      domain: 'challenge',
      httpOnly: false,
      secure: false
    });
    await page.goto(url, {
      waitUntil: 'networkidle0',
      timeout: 3 * 1000,
    });
    await page.close();
  } catch (e) {
    console.log('[-] ERROR');
    console.log('[-]', e);
  }

  console.log(`[+] Crawling finished: ${url}`);
};

const handle = () => {
  console.log('[+] handle');
  connection.blpop('query', 0, async (err, message) => {
    try {
      browser = await init();
      await crawl("http://challenge/?" + message[1]);
      await browser.close();
      setTimeout(handle, 10);
    } catch (e) {}
  });
};

handle();
