#!/bin/bash

# Move to app directory
cd /app || exit

# Setup
touch -a /app/storage/database/database.sqlite
touch -a /app/storage/feeds.txt
mkdir -p /app/storage/framework/cache
mkdir -p /app/storage/framework/sessions
mkdir -p /app/storage/framework/testing
mkdir -p /app/storage/framework/views

php artisan storage:link
php artisan migrate --force
php artisan config:cache

# Set runtime permissions
chown -R www-data:www-data /app

# Run supervisord
supervisord -c /app/docker/services.conf
