(async () => {
  const response = await fetch('main.wasm');
  const bytes = await response.arrayBuffer();

  const output = document.getElementById('output');
  const getchar = program => {
    const it = (function* () {
      for (const c of program) {
        yield c.charCodeAt(0);
      }
    })();
    return () => it.next().value;
  };

  const execute = async program => {
    if (program.length > 1000) {
      alert('too long');
      return;
    }

    let buffer = '';
    output.innerHTML = '';
    const importObject = {
      env: {
        _get_char: getchar(program),
        _print_char(arg) {
          buffer += String.fromCharCode(arg);
        },
        _flush() {
          output.innerHTML += buffer;
          buffer = '';
        }
      }
    };

    const { instance } = await WebAssembly.instantiate(bytes, importObject);
    instance.exports.initialize();
    instance.exports.execute(program.length);
  };

  const edit = document.getElementById('edit');
  const executeButton = document.getElementById('execute');
  executeButton.addEventListener('click', () => {
    execute(edit.value);
  }, false);

  const shareButton = document.getElementById('share');
  shareButton.addEventListener('click', () => {
    prompt('', location.origin + '/#' + edit.value);
  }, false);

  window.addEventListener('hashchange', () => {
    const program = decodeURIComponent(location.hash.slice(1));
    edit.value = program;
    execute(program);
  }, false);

  const program = decodeURIComponent(location.hash.slice(1));
  if (program.length > 1) {
    edit.value = program;
    execute(program);
  }
})();
