# NPM asset build
FROM node:18
COPY . /app
WORKDIR /app
RUN npm ci && npm run build

# Composer dependancies build
FROM composer:2
COPY . /app
WORKDIR /app
RUN composer install

######################
# Main app container #
######################

FROM ubuntu:22.04

# Copy our app files
COPY . /app
WORKDIR /app

# Install dependancies
ARG DEBIAN_FRONTEND=noninteractive
RUN set -xe
RUN apt-get update -yqq
RUN apt-get install curl supervisor nginx cron php8.1-cli php8.1-fpm php8.1-cgi php8.1-common php8.1-curl php8.1-mbstring php8.1-xml php8.1-zip php8.1-gd php8.1-sqlite3 php8.1-bcmath -yqq

# Copy requirements from other containers
COPY --from=0 /app/public/build /app/public/build
COPY --from=1 /app/vendor /app/vendor

# Set permissions and set app key
COPY docker/.env.container /app/.env
RUN php artisan key:generate
RUN php artisan route:cache

# Configure nginx
COPY docker/nginx.conf /etc/nginx/sites-enabled/grabby.conf
RUN rm /etc/nginx/sites-enabled/default
EXPOSE 80/tcp

# Configure cron
COPY docker/cron /etc/cron.d/grabby-cron
RUN chmod 0644 /etc/cron.d/grabby-cron

# Run our process wrapper script
RUN chmod +x /app/docker/run.sh
CMD /app/docker/run.sh

