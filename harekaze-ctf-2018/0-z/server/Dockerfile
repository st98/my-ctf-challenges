FROM node:9.3.0-alpine
MAINTAINER st98 <st98js@gmail.com>
WORKDIR /app

COPY app/ /app
RUN npm install --no-bin-links

EXPOSE 3000
CMD [ "npm", "start" ]