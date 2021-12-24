const path = require('path');
const axios = require('axios');
const fastify = require('fastify');
const Redis = require('ioredis');

const RECAPTCHA_ENABLED = process.env.RECAPTCHA_ENABLED || false;
const RECAPTCHA_SITE_KEY = process.env.RECAPTCHA_SITE_KEY || '[site key is empty]';
const RECAPTCHA_SECRET_KEY = process.env.RECAPTCHA_SECRET_KEY || '[secret key is empty]';
const REDIS_URL = process.env.REDIS_URL || 'redis://127.0.0.1:6379';

const app = fastify();
app.register(require('point-of-view'), {
  engine: {
    ejs: require('ejs')
  }
});
app.register(require('fastify-static'), {
  root: path.join(__dirname, 'static'),
  prefix: '/static/'
});

const redis = new Redis(REDIS_URL);

app.get('/', (req, reply) => {
  reply.view('/views/index.ejs', {
    RECAPTCHA_ENABLED,
    siteKey: RECAPTCHA_SITE_KEY
  });
});

app.post('/report', async (req, reply) => {
  if (!('texts' in req.body && 'token' in req.body)) {
    return reply.send('Parameters are not provided');
  }

  const { texts, token } = req.body;
  if (typeof texts !== 'string' || typeof token !== 'string') {
    return reply.send('Parameters are not string');
  }

  if (RECAPTCHA_ENABLED) {
    const params = `?secret=${RECAPTCHA_SECRET_KEY}&response=${encodeURIComponent(token)}`;
    const url = 'https://www.google.com/recaptcha/api/siteverify' + params;
    const result = await axios.get(url);

    if (!result.data.success) {
      return reply.send('reCAPTCHA failed')
    }
  }

  redis.rpush('query', texts);
  redis.llen('query', (err, result) => {
    console.log('[+] reported:', texts);
    console.log('[+] length:', result);
    reply.send(`Reported. Queue size: ${result}`);
  });
});

const start = async () => {
  try {
    await app.listen(3000, '0.0.0.0');
  } catch (err) {
    app.log.error(err);
    process.exit(1);
  }
};
start();