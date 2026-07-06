#!/bin/sh
set -e

php artisan migrate --force
php artisan storage:link --force
php artisan optimize:clear
