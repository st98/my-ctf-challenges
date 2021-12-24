const fastify = require('fastify');
const path = require('path');
const { flag } = require('./secret');

// generate articles
let articles = [];
for (let i = 0; i < 10000; i++) {
  articles.push({
    title: `Dummy article ${i}`,
    content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'.trim()
  });
}
articles[1337] = {
  title: 'Flag',
  content: `Wow, how do you manage to read this article? Anyway, the flag is: <code>${flag}</code>`
};

const app = fastify({ logger: true });
app.register(require('point-of-view'), {
  engine: {
    ejs: require('ejs')
  },
  root: path.join(__dirname, 'view')
});
app.register(require('fastify-static'), {
  root: path.join(__dirname, 'static'),
  prefix: '/static/',
});

app.get('/', async (request, reply) => {
  return reply.view('index.ejs', { articles });
});

app.get('/article/:id', async (request, reply) => {
  // id should not be negative 
  if (/^[\b\t\n\v\f\r \xa0]*-/.test(request.params.id)) {
    return reply.view('article.ejs', {
      title: 'Access denied',
      content: 'Hacking attempt detected.'
    });
  }

  let id = parseInt(request.params.id, 10);

  // free users cannot read articles with id >9
  if (id > 9) {
    return reply.view('article.ejs', {
      title: 'Access denied',
      content: 'You need to become a premium user to read this content.'
    });
  }

  const article = articles.at(id) ?? {
    title: 'Not found',
    content: 'The requested article was not found.'
  };

  return reply.view('article.ejs', article);
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