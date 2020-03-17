const express = require('express');
const path = require('path');
const vm = require('vm');
const secret = require('./secret');

const app = express();

app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'pug');

app.use(express.static(path.join(__dirname, 'public')));

app.get('/', function (req, res, next) {
  let output = '';
  const code = req.query.code;
  if (code && code.length < 500 && !/eval|Function|constructor|this/.test(code) && !/[^$0-z{}]|[\\]/.test(code)) {
    try {
      output = vm.runInNewContext(code, secret);
    } catch (e) {
      output = e.toString();
    }
  }
  res.render('index', { title: '[$0-z{}]|[^\\\\]', output });
});

app.get('/source', function (req, res) {
  res.sendFile(path.join(__dirname, 'app.js'));
});

module.exports = app;
