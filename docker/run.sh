#!/bin/bash

# Move to app directory
cd /app || exit

# Setup
mkdir -p /app/storage/database
touch -a /app/storage/database/database.sqlite
touch -a /app/storage/feeds.txt
mkdir -p /app/storage/framework/cache
mkdir -p /app/storage/framework/sessions
mkdir -p /app/storage/framework/testing
mkdir -p /app/storage/framework/views

php artisan storage:link
php artisan migrate --force
php artisan config:cache
php artisan view:cache

# Set runtime permissions
chown -R www-data:www-data /app

# Run supervisord
supervisord -c /app/docker/services.conf
