function showError(text) {
  const error = document.getElementById('error');
  error.textContent = text;
  error.classList.remove('hidden');
}

function onSubmit() {
  let content = document.getElementById('content').value;
  content = content.replaceAll('\n', '<br>');
  fetch('/note', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ content })
  }).then(resp => resp.json()).then(resp => {
    if (resp.status === 'ok') {
      location.href = `/note/${resp.message}`;
    } else if (resp.status === 'error') {
      showError(resp.message);
    }
  });
}

window.addEventListener('load', () => {
  const submit = document.getElementById('submit');
  submit.addEventListener('click', onSubmit, false);
}, false);