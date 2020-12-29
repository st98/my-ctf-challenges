const crypto = require('crypto');
const fs = require('fs');
const path = require('path');
const fastify = require('fastify');

const flag = process.env.FLAG || 'flag{DUMMY}';
const users = JSON.parse(fs.readFileSync('./users.json'));
const adminUsername = Object.keys(users).filter(k => k.startsWith('admin'))[0];

const app = fastify({ logger: true });
app.register(require('point-of-view'), {
  engine: {
    ejs: require('ejs')
  },
  root: path.join(__dirname, 'view')
});
app.register(require('fastify-formbody'));
app.register(require('fastify-secure-session'), {
  key: crypto.randomBytes(32),
  cookieName: 'avatar-session'
});
app.register(require('fastify-flash'));
app.register(require('fastify-static'), {
  root: path.join(__dirname, 'static'),
  prefix: '/static/',
});

app.get('/', async (request, reply) => {
  return reply.view('index.ejs', { 
    page: 'home',
    username: request.session.get('username'),
    flash: reply.flash()
  });
});

app.get('/profile', async (request, reply) => {
  const username = request.session.get('username');
  if (!username) {
    request.flash('error', 'please log in to view this page');
    return reply.redirect('/login');
  }

  return reply.view('index.ejs', {
    page: 'profile',
    username,
    flash: reply.flash()
  });
});

app.get('/login', async (request, reply) => {
  return reply.view('index.ejs', { 
    page: 'login',
    username: request.session.get('username'),
    flash: reply.flash()
  });
});

app.get('/admin', async (request, reply) => {
  const username = request.session.get('username');
  if (!username) {
    request.flash('error', 'please log in to view this page');
    return reply.redirect('/login');
  }

  if (username != adminUsername) {
    request.flash('error', 'only admin can view this page');
    return reply.redirect('/login');
  }

  return reply.view('index.ejs', { 
    page: 'admin',
    username: request.session.get('username'),
    flash: reply.flash(),
    flag
  });
});

app.post('/login', async (request, reply) => {
  if (!request.body) {
    request.flash('error', 'HTTP request body is empty');
    return reply.redirect('/login');
  }

  if (!('username' in request.body && 'password' in request.body)) {
    request.flash('error', 'username or password is not provided');
    return reply.redirect('/login');
  }

  const { username, password } = request.body;
  if (username.length > 16) {
    request.flash('error', 'username is too long');
    return reply.redirect('/login');
  }

  if (users[username] != password) {
    request.flash('error', 'username or password is incorrect');
    return reply.redirect('/login');
  }

  request.session.set('username', username);
  reply.redirect('/profile');
});

app.get('/logout', async (request, reply) => {
  request.session.delete();
  reply.redirect('/');
});

app.get('/myavatar.png', async (request, reply) => {
  const username = request.session.get('username');
  if (!username) {
    request.flash('error', 'please log in to view this page');
    return reply.redirect('/login');
  }

  if (username.includes('.') || username.includes('/') || username.includes('\\')) {
    request.flash('error', 'no hacking!');
    return reply.redirect('/login');
  }

  const imagePath = path.normalize(`${__dirname}/images/${username}`);
  if (!imagePath.startsWith(__dirname)) {
    request.flash('error', 'no hacking!');
    return reply.redirect('/login');
  }

  reply.type('image/png');
  if (fs.existsSync(imagePath)) {
    return fs.readFileSync(imagePath);
  }
  return fs.readFileSync('images/default');
});

const start = async () => {
  try {
    await app.listen(3000, '0.0.0.0');
    app.log.info(`server listening on ${app.server.address().port}`);
  } catch (err) {
    app.log.error(err);
    process.exit(1);
  }
};
start();