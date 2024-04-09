# NPM asset build
FROM node:20
COPY . /app
WORKDIR /app
RUN npm ci && npm run build

# Composer dependancies build
FROM composer:2
COPY . /app
WORKDIR /app
RUN composer install --no-dev

######################
# Main app container #
######################

FROM ubuntu:22.04

# Copy our app files
COPY . /app
WORKDIR /app

# Install dependencies
ARG DEBIAN_FRONTEND=noninteractive
RUN set -xe && \
    apt-get update -yqq && \
    apt-get install software-properties-common curl supervisor nginx cron -yqq  && \
    add-apt-repository ppa:ondrej/php && \
    apt-get update -yqq && \
    apt-get install  php8.3-cli php8.3-fpm php8.3-cgi php8.3-common php8.3-curl php8.3-mbstring \
                     php8.3-xml php8.3-zip php8.3-gd php8.3-sqlite3 php8.3-bcmath -yqq

# Copy requirements from other containers
COPY --from=0 /app/public/build /app/public/build
COPY --from=1 /app/vendor /app/vendor

# Make required files changes using passed-though files
# Then create directory for PHP-FPM socket
# Then setup crontab
# Then run any app-side commands
RUN cp docker/.env.container /app/.env && \
    cp docker/nginx.conf /etc/nginx/sites-enabled/rss.conf && \
    rm /etc/nginx/sites-enabled/default && \
    mkdir /run/php && \
    chmod +x /app/docker/run.sh && \
    crontab -u www-data /app/docker/cron && \
    php artisan key:generate && \
    php artisan route:cache

# Run our process wrapper script
EXPOSE 80/tcp
CMD /app/docker/run.sh
