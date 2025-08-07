# DMG Online Read More Link

Add a custom Gutenberg block allowing users to search for posts by ID or title and insert into content as a "Read More" link.

## Key Features

- **DMG Read More custom Gutenberg block** - Allows users to search for posts by ID or title and insert into post content as a link with 'Read More: [Post Title]'
- **WP CLI Command [dmg-read-more-link search]** - Custom WP CLI command to search for posts using the block

## Installation


### Docker Demo

To quickly review and test the plugin you can run the Docker container. 

- Clone repo files
- Run ```make build```
- Run ```make up```

When the container runs it will delete any old posts, seed with 50 posts and add the block to 5 random posts

Omce setup is complete the site can be accessed at **http://localhost:8000**

**Login:** admin / admin

**Note:** you may have to run ```chmod +x``` on some of the shell scripts before

### Standard

- Download the plugin files from the  [releases](https://github.com/addaeabeng/dmg-read-more-link/releases/tag/v0.1.0-alpha)
- Install as normal Wordpress plugin

## Usage

### To use the **DMG Read More Link**:

1. Create or edit and existing post
2. Search for Read More Link or DMG Readmore link in the Gutenberg block inserter
3. Insert the block into the post
4. Click on the block to bring up the block settings
5. Select a post from the latest post or search for a post
6. The block will update to show Read More, followed by the post title

### To use the **dmg-read-more-link** WP CLI command 

1. Open your terminal with access to your Wordpress environment
2. Run the command ```wp dmg-read-more-link search``` (If no paramters are specified it will default to posts made in the last 30 days)
3. You should see a list of post IDs using the block, and the number of results returned. Or get a no posts found message.

### To use the **dmg-read-more-link** WP CLI command in the Docker container

1. Open your terminal in the project root
2. Run the command ```docker compose exec wordpress wp dmg-read-more-link search --allow-root```
3. You should see a list of post IDs using the block, and the number of results returned. Or get a no posts found message.

#### Example CLI usage

Search for posts between a date range

```wp dmg-read-more-link search --date-before=2025-01-01 --date-after=1970-01-01```

Docker container

```docker compose exec wordpress wp dmg-read-more-link search --date-before=2025-01-01 --date-after=1970-01-01 --allow-root```

Find posts in the last 30 days

Run the command with no parameters

```docker compose exec wordpress wp dmg-read-more-link search --date-before=2025-01-01 --date-after=1970-01-01 --allow-root```

## Development

I built the block using the ```@wordpress/scripts``` package. For testing i used Jest and PHPUnit with the WP Test Suite. I developed this plugin in a custom local Docker environment for portability.

### Performance considerations

To handle large datasets I have implemented the following:

#### Utility Taxonomy

For optimal performance on large datasets the block uses a utility taxonomy ```dmg-read-more-link```. This avoids the overhead of searching the post content for each post. When posts are created or updated using the block they are added to this taxonomy. If the block is removed from a post, the post is removed from the taxonomy. The reasoning behind this is explained in some detail [here](https://tomjn.com/2018/03/16/utility-taxonomies/)

#### Query batching

Instead of fetching all the posts at once, they are retrieved in batches of 100. This a common WordPress best practice for retrieving large numbers of posts with WP_Query and reduces load on the database. 

### Error Handling

If no posts are found a message is displayed to the user, rather than failing silently.

If the dates entered are invalid then the wp cli falls back to displaying posts in the last 30 days.

### TODO

Due to time constraints I still consider this plugin to be WIP, but the basic functionality is working. A summary of improvements and known issues.

#### To be completed

1. Code quality. The linting config needs to be completed and running with the relevant WP coding standards
2. Testing. I was unable to complete the PHP testing, mainly for the WP CLI commands. 
3. Docker environment. There are some improvements I can make to the setup process and the Makefile.
4. The block UI is basic, and the display of the posts could be improved. The Read more preview link could also be disabled.
5. Deployment Scripts. There arent any precommit checks using ```husky``` or deployment scripts for Github to run tests. 
6. Testing and Development documentation.

#### Known Issues
1. When running the seed script sometimes throws an error when randomly assigning the block to seeded posts. If this happens, then just run ```make down``` then ```make up``` or run ```make seed``` to re run the seeding process.
2. An error message appears when searching for posts. This does not affect functionality but its something I need to look at.










