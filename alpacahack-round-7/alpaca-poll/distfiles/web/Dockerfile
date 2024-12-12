FROM node:22

WORKDIR /app

RUN apt update -y && apt install -y redis

COPY package.json package-lock.json ./
RUN npm install

COPY . .
RUN chmod +x /app/start.sh

USER 404:404
CMD ["/app/start.sh"]