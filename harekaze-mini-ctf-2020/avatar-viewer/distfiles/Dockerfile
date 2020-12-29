FROM node:15

WORKDIR /usr/src/app

COPY app/package*.json ./

RUN npm install --only=production

COPY app/. .

ENV FLAG "HarekazeCTF{<censored>}"

USER node

CMD [ "npm", "start" ]