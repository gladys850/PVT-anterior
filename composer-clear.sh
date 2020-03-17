#!/usr/bin/env bash

composer dump-autoload
php artisan vendor:publish --all
php artisan view:clear
php artisan route:clear
php artisan cache:clear
php artisan config:clear
php artisan clear-compiled