FROM node:14
WORKDIR /app

COPY challenge/ /app
RUN npm install --no-bin-links

EXPOSE 3000
CMD [ "npm", "start" ]