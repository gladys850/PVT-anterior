#!/usr/bin/env bash

APP_ENV=$(grep APP_ENV .env | cut -d '=' -f2)
PROD="production"

rm -rf public/js/app.js
if [[ "$APP_ENV" == "$PROD" ]]; then
    yarn install --production --force
    yarn prod
else
    yarn install
    yarn dev
fi