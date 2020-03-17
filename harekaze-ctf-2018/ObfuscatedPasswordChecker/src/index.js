var flag = 'HarekazeCTF{j4v4scr1pt-0bfusc4t0r_1s_tsur41}';

window.addEventListener('load', function () {
    var input = document.getElementById('password');
    var button = document.getElementById('button');
    var info = document.getElementById('info');
    button.addEventListener('click', () => {
        var password = input.value;
        info.style.display = 'block';
        if (password === flag) {
            info.className = 'success';
            info.textContent = 'congratz! the flag is: ' + flag;
        } else {
            info.className = 'error';
            info.textContent = 'nope...';
        }
    }, false);
}, false);