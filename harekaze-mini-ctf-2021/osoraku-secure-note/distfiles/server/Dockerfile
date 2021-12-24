FROM node:17-slim

WORKDIR /app
COPY package.json ./
RUN npm install

COPY index.js ./
COPY static/ static/
COPY views/ views/

USER node

EXPOSE 3000
CMD ["node", "index.js"]