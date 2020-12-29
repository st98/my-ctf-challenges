/* (snipped) */
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
/* (snipped) */