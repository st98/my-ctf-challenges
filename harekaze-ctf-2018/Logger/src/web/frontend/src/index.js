import '../semantic/dist/semantic.js';

function sha256(s) {
  return crypto.createHash('sha256').update(s).digest('hex');
}

$(function () {
  $('#submit').on('click', function () {
    var username = $('input#username').val();
    var password = $('input#password').val();
    var nonce = $('input#nonce').val();
    var cnonce = Math.random() + '';
    var hash = sha256(sha256(password) + ':' + nonce + ':' + cnonce);

    $.post('login.php', {
      username: username,
      cnonce: cnonce,
      hash: hash
    }, function (data) {
      if (data.error) {
        $('input#nonce').val(data.nonce);
      } else {
        location.reload(true);
      }
    });

    return false;
  });
});