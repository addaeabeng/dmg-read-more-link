# DMG Online Read More Link

Add a custom Gutenberg block allowing users to search for posts by ID or title and insert into content as a "Read More" link.

## Key Features

- **DMG Read More custom Gutenberg block** - Allows users to search for posts by ID or title and insert into post content as a link with 'Read More: [Post Title]'
- **WP CLI Command [dmg-read-more-link search]** - Custom WP CLI command to search for posts using the block

## Installation

There is no installation! Well, maybe a little. If you just want to see the plugin in action you can just download the repo and run the commands in the Makefile.

## Docker

Clone repo files
Run make build
Run make up

When the container runs it will delete any old posts, seed with 50 posts and add the block to 5 random posts

Omce setup is complete the site can be accessed at http://localhost:8000

Login: admin / admin

## Standard

Copy the folder dmg-read-more-link from wp-content/plugins 
Install as normal Wordpress plugin

Development





