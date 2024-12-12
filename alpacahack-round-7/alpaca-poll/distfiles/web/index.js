import fs from 'node:fs/promises';
import express from 'express';

import { init, vote, getVotes } from './db.js';

const PORT = process.env.PORT || 3000;
const FLAG = process.env.FLAG || 'Alpaca{dummy}';

process.on('uncaughtException', (error) => {
    console.error('Uncaught Exception:', error);
});

const app = express();
app.use(express.urlencoded({ extended: false }));
app.use(express.static('static'));

const indexHtml = (await fs.readFile('./static/index.html')).toString();
app.get('/', async (req, res) => {
    res.setHeader('Content-Type', 'text/html');
    return res.send(indexHtml);
});

app.post('/vote', async (req, res) => {
    let animal = req.body.animal || 'alpaca';

    // animal must be a string
    animal = animal + '';
    // no injection, please
    animal = animal.replace('\r', '').replace('\n', '');

    try {
        return res.json({
            [animal]: await vote(animal)
        });
    } catch {
        return res.json({ error: 'something wrong' });
    }
});

app.get('/votes', async (req, res) => {
    return res.json(await getVotes());
});

await init(FLAG); // initialize Redis
app.listen(PORT, () => {
    console.log(`server listening on ${PORT}`);
});