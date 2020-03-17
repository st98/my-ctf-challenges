const WebSocket = require('ws');

const wss = new WebSocket.Server({ host: '0.0.0.0', port: 7467 })

function encode(s, t) {
  var r = [];
  if (typeof s === 'string') {
    s = s.split('').map(c => c.charCodeAt(0));
  }
  var i, z;
  for (i = 0; i < s.length; i++) {
    if (s[i]) {
      break;
    }
  }
  z = i;
  for (; i < s.length; i++) {
    var c = s[i];
    var j;
    for (j = 0; j in r || c; j++) {
      if (r[j]) {
        c += r[j] * 256;
      }
      r[j] = c % 58;
      c = Math.floor(c / 58);
    }
  }
  return t[0].repeat(z) + r.reverse().map(function (x) {
    return t[x];
  }).join('');
}

function decode(s, t) {
  var u = Array(256);
  var i, z;
  var r = [];
  for (i = 0; i < 58; i++) {
    u[t[i]] = i;
  }
  for (i = 0; i < s.length; i++) {
    if (s[i] !== t[0]) {
      break;
    }
  }
  z = i;
  for (; i < s.length; i++) {
    var j;
    var c = u[s[i]];
    for (j = 0; j in r || c; j++) {
      if (r[j]) {
        c += r[j] * 58;
      }
      r[j] = c % 256;
      c = Math.floor(c / 256);
    }
  }
  return Array(z).fill(0).concat(r.reverse());
}

function hash(s) {
  var r = 0, i;
  for (i = 0; i < s.length; i++) {
    r = r * 31 + s.charCodeAt(i) | 0;
  }
  return r;
}

function rand(s) {
  var x = 123456789;
  var y = 362436069;
  var z = 521288629;
  var w = 88675123;
  var t;
  return function (min, max) {
    t = x ^ (x << 11);
    x = y; y = z; z = w;
    w = (w ^ (w >> 19)) ^ (t ^ (t >> 8));
    if (min !== undefined && max !== undefined) {
      return min + (w % (max + 1 - min));
    }
    return w;
  };
}

function shuffle(a, r) {
  var i;
  for (i = a.length - 1; i > 0; i--) {
    var j = Math.abs(r(0, i));
    var tmp = a[i];
    a[i] = a[j];
    a[j] = tmp;
  }
}

var INITIAL_TABLE = 'MeitamANbcfv2yXDH1RjPTzVqnLYFhE54uJUkdwCgGB36srQ8o9ZK7WxSp';
wss.on('connection', function connection(ws) {
  var table = INITIAL_TABLE;

  ws.on('message', function (message) {
    if (!table.indexOf('MeitamA')) {
      var userAgent = String.fromCharCode.apply(null, decode(message, table));
      table = table.split('');
      shuffle(table, rand(hash(userAgent)));
      table = table.join('');
    } else {
      console.log('received: %s', String.fromCharCode.apply(null, decode(message, table)));
    }
  });

  ws.on('error', function () {});
});