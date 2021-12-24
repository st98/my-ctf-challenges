const path = require('path');
const axios = require('axios');
const fastify = require('fastify');
const Redis = require('ioredis');
const { JSDOM } = require('jsdom');
const { v4: uuidv4 } = require('uuid');

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
  reply.view('/views/index.ejs');
});

const notes = new Map();
notes.set('00000000-0000-0000-0000-000000000000', {
  timestamp: 0,
  content: 'Welcome to <strong>Osoraku Secure Note</strong>!<br><br>This is a note-taking service that you can share notes with your friends <em>secretly</em>. Note IDs are <em>completely random</em>, so no one can steal your notes.<br><br>You can use some HTML tags, such as &lt;del&gt;, and &lt;em&gt; to make pretty notes.<br><br>If you find any problems on notes, please report to admin. Admin will check it immediately.'
});

// this check ensures that the input doesn't have dangerous HTML tags!
function isSafe(node) {
  if (![...node.childNodes].every(isSafe)) {
    return false;
  }

  if (!['#text', '#document-fragment', 'BR', 'DEL', 'EM', 'STRONG'].includes(node.nodeName)){
    return false;
  }

  return true;
}

app.post('/note', (req, reply) => {
  const { content } = req.body;
  if (typeof content !== 'string' || content.length === 0) {
    return reply.send({ status: 'error', message: 'No content is provided' });
  }

  const fragment = JSDOM.fragment(content);
  if (![...fragment.childNodes].every(isSafe)) {
    return reply.send({ status: 'error', message: 'Prohibited HTML tags found!' });
  }

  const id = uuidv4();
  notes.set(id, {
    timestamp: Date.now(), content
  });

  return reply.send({ status: 'ok', message: id });
});

app.get('/note/:id', (req, reply) => {
  const { id } = req.params;
  if (!/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/.test(id) || !notes.has(id)) {
    return reply.view('/views/note.ejs', {
      error: 'Note not found'
    });
  }

  const note = notes.get(id);
  return reply.view('/views/note.ejs', {
    error: null, note,
    RECAPTCHA_ENABLED,
    siteKey: RECAPTCHA_SITE_KEY,
  });
});

app.post('/report', async (req, reply) => {
  if (!('id' in req.body && 'token' in req.body)) {
    return reply.send('Parameters are not provided');
  }

  const { id, token } = req.body;
  if (typeof id !== 'string' || typeof token !== 'string') {
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

  redis.rpush('query', id);
  redis.llen('query', (err, result) => {
    console.log('[+] reported:', id);
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