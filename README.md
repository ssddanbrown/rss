### TODO

- Testing coverage
- Tag filtering
- Feed access tracking
- Feed reload command
   - Active feeds only by default
   - Option to reload all outdated
- Docker setup
- GH sponsors info

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
