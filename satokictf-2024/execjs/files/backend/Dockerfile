FROM node:20.11.0-bookworm-slim

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm i

COPY . .

# disable HTTP APIs for debugging just to be sure
CMD ["node", "--inspect-publish-uid=stderr", "index.js"]