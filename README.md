### Next Steps

ConfigLoader -> for the logic of loading the logic and creating relavent feeds
depending on config/session.

### Setup

```shell

# Create database
touch storage/database/database.sqlite

```

### Config Format

```txt
https://feed.url.com/feed.xml feed-name #tag-a #tag-b
https://example.com/feed.xml Example-Site #updates #news

# Lines starting with a hash are considered comments
# Empty lines are fine and will be ignored
```

### RSS Info

- Spec: https://cyber.harvard.edu/rss/rss.html#comments

#### Feed URLs For Testing

https://www.bookstackapp.com/blog/index.xml
http://feeds.bbci.co.uk/news/uk/rss.xml
https://feeds.arstechnica.com/arstechnica/index
