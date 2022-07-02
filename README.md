# RSS

A simple, opinionated, RSS feed aggregator.

[![PHPUnit](https://github.com/ssddanbrown/rss/actions/workflows/phpunit.yml/badge.svg?branch=main)](https://github.com/ssddanbrown/rss/actions/workflows/phpunit.yml)

## Features

The following features are built into the application:

- Supports RSS and ATOM formats.
- Regular auto-fetching of RSS feeds (Every hour).
- Custom feed names and colors.
- Feed-based tags for categorization.
- 3 different post layout modes (card, list, compact).
- Fetching of page open-graph images.
- Managed via a single plaintext file.
- System-based dark/light theme.
- Post title/description search.
- Ready-to-use docker image.
- Mobile screen compatible.

## Limitations

The below possibly expected features are missing from this application.
This is not a list of planned features. Please see the [Low Maintenance Project](#low-maintenance-project) section below for more info.

- No import of full post/article content.
- No feed management via the UI.
- No user authentication or management system.
- No customization, extension or plugin system.
- No organisation upon feed-level tagging.

Upon the above, it's quite likely you'll come across issues. This project was created to meet a personal need while learning some new technologies. Much of the logic is custom written instead of using battle-tested libraries. 

## Screenshots


<table>
	<tbody>
		<tr>
			<td width="25%">
				Card View
				<img src="https://github.com/ssddanbrown/rss/raw/main/.github/screenshots/card-view.png">
			</td>
			<td width="25%">
				List View
				<img src="https://github.com/ssddanbrown/rss/raw/main/.github/screenshots/list-view.png">
			</td>
			<td width="25%">
				Compact View
				<img src="https://github.com/ssddanbrown/rss/raw/main/.github/screenshots/compact-view.png">
			</td>
			<td width="25%">
				Dark Mode
				<img src="https://github.com/ssddanbrown/rss/raw/main/.github/screenshots/dark-mode.png">
			</td>
		</tr>
	</tbody>
</table>


## Docker Usage

A pre-built docker image is available to run the application. 
Storage data is confined to a single `/app/storage` directory for easy volume mounting.
Port 80 is exposed by default for application access. This application does not support HTTPS, for that you should instead use a proxy layer such as nginx.

#### Docker Run Command Example

In the below command, the application will be accessible at http://localhost:8080 on the host and the files would be stored in a `/home/barry/rss` directory. In this example, feeds would be configured in a `/home/barry/rss/feeds.txt` file.

```shell
docker run -d \
    --restart unless-stopped \
    -p 8080:80 \
    -v /home/barry/rss:/app/storage \
    ghcr.io/ssddanbrown/rss:latest  
```

#### Docker Compose Example

In the below `docker-compose.yml` example, the application will be accessible at http://localhost:8080 on the host and the files would be stored in a `/home/barry/rss` directory. In this example, feeds would be configured in a `/home/barry/rss/feeds.txt` file.

```yml
---
version: "2"
services:
    rss:
        image: ghcr.io/ssddanbrown/rss:latest
        container_name: rss
        environment:
            - APP_NAME=RSS
        volumes:
            - /home/barry/rss:/app/storage
        ports:
            - "8080:80"
        restart: unless-stopped
```


### Building the Docker Image

If you'd like to build the image from scratch, instead of using the pre-built image, you can do so like this:

```shell
docker build -f docker/Dockerfile .
```

## Feed Configuration

Feed configuration is handled by a plaintext file on the host system.
By default, using our docker image, this configuration would be located in a `feeds.txt` file within the path you mounted to `/app/storage`.

The format of this file can be seen below:

```txt
https://feed.url.com/feed.xml feed-name #tag-a #tag-b
https://example.com/feed.xml Example #updates #news

# Lines starting with a hash are considered comments.
# Empty lines are fine and will be ignored.

# Underscores in names will be converted to spaces.
https://example.com/feed-b.xml News_Site #news

# Feed color can be set using square brackets after the name.
# The color must be a CSS-compatible color value.
https://example.com/feed-c.xml Blue_News[#0078b9] #news #blue
```

## App Configuration

The application allows some configuration through variables.
These can be set via the `.env` file or, when using docker, via environment variables.

```shell
# The name of the application.
# Only really shown in the title/browser-tab.
APP_NAME=RSS

# The path to the config file.
# Defaults to `storage/feeds.txt` within the application folder.
APP_CONFIG_FILE=/app/storage/feeds.txt

# Enable or disable the loading of post thumbnails.
# Does not control them within the UI, but controls the fetching
# when posts are fetched.
# Defaults to true.
APP_LOAD_POST_THUMBNAILS=true
```

## Manual Install

Manually installing the application is not recommended unless you are performing development work on the project.
Instead, use of the docker image is advised.

This project is based upon Laravel so the requirements and install process are much the same.
You will need git, PHP, composer and NodeJS installed. Installation would generally be as follows:

```shell
# Clone down and enter the project
git clone https://github.com/ssddanbrown/rss.git
cd rss

# Install PHP dependencies via composer
# This will check you meet the minimum PHP version and extensions required.
composer install

# Create database file
touch storage/database/database.sqlite

# Copy config, generate app key, migrate database & link storage
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link

# Install JS dependencies & build CSS/JS
npm install
npm run build
```

For a production server you'd really want to have a webserver active to server the `public` directory and handle PHP.
You'd also need a process to run the laravel queue system in addition to a cron job to run the schedule.

On a development system, These can be done like so:

```shell
# Serve the app
php artisan serve

# Watch the queue
php artisan queue:listen

# Work the schedule
php artisan schedule:work
```

## Low Maintenance Project

This is a low maintenance project. The scope of features and support are purposefully kept narrow for my purposes to ensure longer term maintenance is viable. I'm not looking to grow this into a bigger project at all.

Issues and PRs raised for bugs are perfectly fine assuming they don't significantly increase the scope of the project. Please don't open PRs for new features that expand the scope.

## Attribution

This is primarily built using the following great projects and technologies:

- [Laravel](https://laravel.com/) - [MIT License](https://github.com/laravel/framework/blob/9.x/LICENSE.md)
- [InertiaJS](https://inertiajs.com/) - [MIT License](https://github.com/inertiajs/inertia/blob/master/LICENSE)
- [SQLite](https://www.sqlite.org/index.html) - [Public Domain](https://www.sqlite.org/copyright.html)
- [TailwindCSS](https://tailwindcss.com/) - [MIT License](https://github.com/tailwindlabs/tailwindcss/blob/master/LICENSE)
