FROM php:8.2-cli-alpine

COPY ./ /api_emulator
WORKDIR /api_emulator

ENV INGENERATOR_ENV=standalone
ENV HANDLERS_FILE=/api_emulator/default_handlers/handlers.php
ENV PORT=80

ENTRYPOINT ["/api_emulator/entrypoint.sh"]
