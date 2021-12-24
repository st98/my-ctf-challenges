// since query string is complex like `?text0.size=...`, 
// I need to implement a parser instead of using URLSearchParams...
function parseQueryString(query) {
  let res = {};

  for (const param of query.split('&')) {
    const [key, value] = param.split('=');
    const parts = key.split('.');

    let tmp = res;
    for (const part of parts.slice(0, -1)) {
      // I know there is a vulnerability called Prototype Pollution :)
      // so if suspicious property is found, raise an error
      if (part === '__proto__') {
        throw new Error('HACKING ATTEMPT DETECTED!');
      }

      if (!(part in tmp)) {
        tmp[part] = {};
      }
      tmp = tmp[part];
    }

    tmp[parts[parts.length - 1]] = decodeURIComponent(value);
  }
  
  return res;
}

// sorry for the ugly code!
function newTextMenu(i, text) {
  const div = document.createElement('div');
  div.innerHTML = `<h2>Text ${i}</h2>
  <div>
    <label>X: <input type="number" name="x" min="8" max="256" value="48"></label>
  </div>
  <div>
    <label>Y: <input type="number" name="y" min="8" max="256" value="160"></label>
  </div>
  <div>
    <label>Size: <input type="number" name="size" min="8" max="256" value="128"></label>
  </div>
  <div>
    <label>Color: <input type="color" name="color" value="#c13333"></label>
  </div>
  <div>
    <label for="text">Text:</label>
    <textarea name="text" cols="6" rows="5">:D</textarea>
  </div>
</div>`;

  [...div.querySelectorAll('input, textarea')].forEach(e => {
    e.value = text[e.name];
    e.addEventListener('change', () => {
      text[e.name] = e.value;
      render();
    }, false);
  });

  return div;
}

const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');

// parse query string
const queryString = location.search.slice(1);
let texts = [];
if (queryString.length > 0) {
  const obj = parseQueryString(queryString);
  let res = [];

  let i = 0;
  while (`text${i}` in obj) {
    res.push(obj[`text${i}`]);
    i++;
  }

  texts = res;
} else {
  texts.push({
    x: 48,
    y: 160,
    size: 128,
    color: '#c13333',
    text: ':D'
  });
}

function renderText(text) {
  ctx.font = `${text.size}px sans-serif`;
  ctx.fillStyle  = text.color;
  ctx.fillText(text.text, text.x, text.y);
}

function render() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  texts.forEach(renderText);
}

for (let i = 0; i < texts.length; i++) {
  document.getElementById('texts').appendChild(newTextMenu(i + 1, texts[i]));
}
render();

// add text menu
document.getElementById('add').addEventListener('click', () => {
  const text = {
    x: 48,
    y: 160,
    size: 128,
    color: '#c13333',
    text: ':D'
  };
  texts.push(text);

  document.getElementById('texts').appendChild(newTextMenu(texts.length, text));
  render();
}, false);

// save image
document.getElementById('save').addEventListener('click', () => {
  const a = document.createElement('a');
  a.download = 'emoji.png';
  a.href = canvas.toDataURL('image/png');
  a.click();
}, false);

// get url to share
function serialize(texts) {
  let res = [];
  for (let i = 0; i < texts.length; i++) {
    for (const [key, value] of Object.entries(texts[i])) {
      res.push(`text${i}.${key}=${encodeURIComponent(value)}`);
    }
  }

  return res.join('&');
}

document.getElementById('geturl').addEventListener('click', () => {
  prompt('', `${location.origin}?${serialize(texts)}`);
}, false);

// share with admin!!
async function onSubmit(token) {
  const button = document.getElementById('report');

  const serializedTexts = serialize(texts);
  const result = await (await fetch('/report', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ texts: serializedTexts, token })
  })).text();
  button.textContent = result;
}