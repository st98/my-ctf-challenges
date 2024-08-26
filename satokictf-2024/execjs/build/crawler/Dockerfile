FROM node:22-bookworm-slim

ENV PLAYWRIGHT_SKIP_BROWSER_DOWNLOAD=1

WORKDIR /app

RUN apt update -y && apt install -y chromium
COPY package.json /app/
RUN npm install
RUN npx playwright install-deps chromium

RUN groupadd -r appuser && useradd -r -g appuser -G audio,video appuser \
    && mkdir -p /home/appuser/Downloads \
    && chown -R appuser:appuser /home/appuser \
    && chown -R appuser:appuser /app/node_modules
USER appuser

COPY index.js /app/

ENTRYPOINT [ "node", "index.js" ]