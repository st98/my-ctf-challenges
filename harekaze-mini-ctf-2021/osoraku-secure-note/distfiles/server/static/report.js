async function onSubmit(token) {
  const button = document.getElementById('report');

  const id = location.pathname.split('/').slice(-1)[0];
  const result = await (await fetch('/report', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ id, token })
  })).text();
  button.textContent = result;
}