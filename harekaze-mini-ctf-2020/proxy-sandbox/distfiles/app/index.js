const fastify = require('fastify');
const fs = require('fs');
const path = require('path');
const pug = require('pug');
const vm = require('vm');

const app = fastify({ logger: true });
app.register(require('point-of-view'), {
  engine: { pug },
  root: path.join(__dirname, 'view')
});
app.register(require('fastify-static'), {
  root: path.join(__dirname, 'static'),
  prefix: '/static/',
});

const FLAG = 'HarekazeCTF{<censored>}';
const template = `
// no prototype pollution!
Object.freeze(Object.prototype);
Object.freeze(Object);
// no hoisting!
const Proxy = globalThis.Proxy;

const obj = new Proxy({
  flag: '${FLAG}'
}, {
  get(target, prop) {
    if (prop === 'flag' || !(prop in target)) {
      return undefined;
    }
    return target[prop];
  },
  getOwnPropertyDescriptor(target, prop) {
    if (prop === 'flag' || !target.hasOwnProperty(prop)) {
      return undefined;
    }
    return Reflect.getOwnPropertyDescriptor(...arguments);
  }
});

CODE
`;

app.get('/', async (request, reply) => {
  let output = '';
  const code = request.query.code + '';

  if (code && code.length < 150) {
    try {
      const result = vm.runInNewContext(template.replace('CODE', code), {}, { timeout: 500 });
      output = result + '';
    } catch (e) {
      output = 'nope';
    }
  } else {
    output = 'nope';
  }

  return reply.view('index.pug', {
    title: 'Proxy Sandbox', output
  });
});

app.get('/source', async (request, reply) => {
  const code = fs.readFileSync(__filename).toString();
  reply.type('application/javascript').code(200);
  reply.send(code.replace(/HarekazeCTF\{.+\}/, 'HarekazeCTF{<censored>}'));
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