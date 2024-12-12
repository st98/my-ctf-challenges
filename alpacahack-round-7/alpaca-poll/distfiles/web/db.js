import net from 'node:net';

function connect() {
    return new Promise(resolve => {
        const socket = net.connect('6379', 'localhost', () => {
            resolve(socket);
        });
    });
}

function send(socket, data) {
    console.info('[send]', JSON.stringify(data));
    socket.write(data);

    return new Promise(resolve => {
        socket.on('data', data => {
            console.info('[recv]', JSON.stringify(data.toString()));
            resolve(data.toString());
        })
    });
}

export async function vote(animal) {
    const socket = await connect();
    const message = `INCR ${animal}\r\n`;

    const reply = await send(socket, message);
    socket.destroy();

    return parseInt(reply.match(/:(\d+)/)[1], 10); // the format of response is like `:23`, so this extracts only the number 
}

const ANIMALS = ['dog', 'cat', 'alpaca'];
export async function getVotes() {
    const socket = await connect();

    let message = '';
    for (const animal of ANIMALS) {
        message += `GET ${animal}\r\n`;
    }

    const reply = await send(socket, message);
    socket.destroy();

    let result = {};
    for (const [index, match] of Object.entries([...reply.matchAll(/\$\d+\r\n(\d+)/g)])) {
        result[ANIMALS[index]] = parseInt(match[1], 10);
    }

    return result;
}

export async function init(flag) {
    const socket = await connect();

    let message = '';
    for (const animal of ANIMALS) {
        const votes = animal === 'alpaca' ? 10000 : Math.random() * 100 | 0;
        message += `SET ${animal} ${votes}\r\n`;
    }

    message += `SET flag ${flag}\r\n`; // please exfiltrate this

    await send(socket, message);
    socket.destroy();
}