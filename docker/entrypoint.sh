#!/bin/bash
set -e

echo 'Caching config...'
php artisan config:cache

echo 'Caching routes...'
php artisan route:cache

echo 'Running migrations...'
php artisan migrate --force

echo 'Running storage...'
php artisan storage:link

echo 'Running queue...'
php artisan queue:restart
php artisan queue:work &

echo 'Running schedule...'
php artisan schedule:work &

exec "$@"
