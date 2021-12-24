FROM node:16-buster-slim

WORKDIR /usr/src/app

COPY app/package*.json ./

RUN npm install --only=production

COPY app/ .

USER node

EXPOSE 3000

CMD [ "npm", "start" ]