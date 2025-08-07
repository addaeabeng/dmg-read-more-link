#!/bin/bash
export WP_CLI_ALLOW_ROOT=1

set -e
echo "🚀 wp-seed.sh started"
touch /tmp/seed-script-ran

# ✅ Parse flags
RESET=false
for arg in "$@"; do
  if [[ "$arg" == "--reset" ]]; then
    RESET=true
  fi
done

# ✅ Wait for WordPress core files
echo "⏳ Waiting for WordPress core files..."
until [ -f /var/www/html/wp-includes/version.php ]; do
  sleep 1
done
echo "✅ WordPress core files detected."

# ✅ Wait for MySQL
echo "⏳ Waiting for MySQL to accept connections..."
until mysqladmin ping -h"$WORDPRESS_DB_HOST" --silent; do
  sleep 2
done
echo "✅ MySQL is responding."

# ✅ Create wp-config.php if missing
if [ ! -f /var/www/html/wp-config.php ]; then
  echo "⚙️ Generating wp-config.php..."
  wp config create \
    --dbname="$WORDPRESS_DB_NAME" \
    --dbuser="$WORDPRESS_DB_USER" \
    --dbpass="$WORDPRESS_DB_PASSWORD" \
    --dbhost="$WORDPRESS_DB_HOST" \
    --skip-check \
    --allow-root
fi

# ✅ Wait for DB via WP-CLI
echo "⏳ Waiting for WP-CLI to access DB..."
until wp db check --allow-root > /dev/null 2>&1; do
  sleep 2
done
echo "✅ WP-CLI confirms DB ready."

# ✅ Install WordPress if not already
if wp core is-installed --allow-root; then
  echo "✅ WordPress already installed."
else
  echo "🚀 Installing WordPress..."
  wp core install \
    --url="http://localhost:8000" \
    --title="Read More Link Dev" \
    --admin_user=admin \
    --admin_password=admin \
    --admin_email=admin@example.com \
    --skip-email \
    --allow-root
fi

# ✅ Activate plugin
echo "🔌 Activating plugin..."
wp plugin activate dmg-read-more-link --allow-root || true

# ✅ Optional reset
if [ "$RESET" = true ]; then
  echo "🧹 Deleting all posts..."

  # Get list of post IDs
  mapfile -t post_ids < <(wp post list --post_type=post --format=ids --allow-root | tr ' ' '\n')
 # Check how many posts exist before deleting
  echo "🔍 Found ${#post_ids[@]} posts to delete."


  count=${#post_ids[@]}

  if [ "$count" -eq 0 ]; then
    echo "ℹ️ No posts found to delete."
  else
    echo "🗑️ Deleting $count posts..."
    current=0
    echo "📝 Post IDs to delete: ${post_ids[*]}"
    for id in "${post_ids[@]}"; do
      wp post delete "$id" --force --allow-root > /dev/null 2>&1 || true
      current=$((current + 1))
      progress=$((current * 100 / count))
      printf "\r🔄 Progress: [%-50s] %d%%" "$(printf "%0.s#" $(seq 1 $((progress / 2))))" "$progress"
    done

    echo -e "\n✅ Deleted $count posts."

    remaining=$(wp post list --post_type=post --format=ids --allow-root | wc -w)
    echo "📦 Remaining posts after deletion: $remaining"
  fi
fi


# ✅ Dummy titles and bodies
TITLES=(
  "How to Build a WordPress Plugin"
  "10 Things I Learned From Open Source"
  "Debugging Docker: A Survival Guide"
  "PHP Is Not Dead: Here's Why"
  "Understanding Gutenberg Blocks"
  "A Day in the Life of a Dev"
  "Best Practices for Modern WordPress Development"
  "Scaling WordPress with Docker"
  "The Future of CMS in 2025"
  "What I Wish I Knew About WP-CLI"
)

BODIES=(
  "In this post, we'll explore the ins and outs of building a WordPress plugin from scratch."
  "Here are ten hard-earned lessons I've picked up from contributing to open source projects."
  "Ever been stuck debugging a Docker container? You're not alone. Here's how I survived."
  "Despite what you may hear, PHP is alive and well — and powering the web quietly."
  "Learn how to register and render Gutenberg blocks like a pro developer."
  "Here's what a typical day looks like when you're a full-time developer in 2025."
  "Security, performance, and flexibility — that's what modern WP dev is all about."
  "Yes, you can scale WordPress with Docker — here's how I did it for my last project."
  "Is WordPress still relevant? Here's what the data and trends suggest."
  "From `wp plugin` to `wp export`, this CLI tool has saved me hours — here's why."
)

# ✅ Create 50 posts
echo "📝 Creating 50 dummy posts..."
target_total=50
created=0

while [[ $created -lt $target_total ]]; do
  title="${TITLES[$((RANDOM % ${#TITLES[@]}))]}"
  content="${BODIES[$((RANDOM % ${#BODIES[@]}))]}"

  post_id=$(wp post create \
    --post_type=post \
    --post_status=publish \
    --post_title="$title" \
    --post_content="$content" \
    --porcelain \
    --allow-root)

  if [[ -n "$post_id" && "$post_id" =~ ^[0-9]+$ ]]; then
    wp post meta add "$post_id" _is_seeded true --allow-root > /dev/null
    created=$((created + 1))
    progress=$((created * 100 / target_total))
    printf "\r🔄 Progress: [%-50s] %d%%" $(printf "%0.s#" $(seq 1 $((progress / 2)))) "$progress"
  else
    echo -e "\n⚠️ Failed to create post. Retrying..."
  fi
done

echo -e "\n✅ Finished creating $total dummy posts."

# ✅ Get 5 random seeded posts to inject block into
for target_post_id in $(wp post list \
  --post_type=post \
  --meta_key=_is_seeded \
  --meta_value=true \
  --format=ids \
  --orderby=rand \
  --posts_per_page=5 \
  --allow-root); do

  # ✅ Select a different random post to link to
  link_post_id=$(wp post list \
    --post_type=post \
    --meta_key=_is_seeded \
    --meta_value=true \
    --format=ids \
    --orderby=rand \
    --posts_per_page=1 \
    --allow-root | grep -v "^$target_post_id" | head -n 1)

  link_title=$(wp post get "$link_post_id" --field=post_title --allow-root | sed 's/"/\\"/g')
  link_url=$(wp post get "$link_post_id" --field=guid --allow-root)

  block_content=$(cat <<EOF
<!-- wp:dmg/read-more-link {"postId":$link_post_id,"postTitle":"$link_title","postUrl":"$link_url"} -->
<p class="wp-block-dmg-read-more-link">Read More: <a href="$link_url">$link_title</a></p>
<!-- /wp:dmg/read-more-link -->
EOF
  )

  existing_content=$(wp post get "$target_post_id" --field=post_content --allow-root)
  updated_content="$existing_content"$'\n\n'"$block_content"

  wp post update "$target_post_id" --post_content="$updated_content" --allow-root
done


# ✅ Set permalink structure
echo "🔧 Setting permalinks to '/%postname%/'..."
wp rewrite structure '/%postname%/' --hard --allow-root
wp rewrite flush --hard --allow-root

echo "✅ Setup complete! Visit: http://localhost:8000 (admin / admin)"
