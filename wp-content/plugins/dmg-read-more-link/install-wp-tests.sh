#!/bin/bash
set -e

# Configuration
: "${WP_VERSION:=latest}"
: "${WP_TESTS_DIR:=/tmp/wordpress-tests-lib}"
: "${WP_CORE_DIR:=$WP_TESTS_DIR/src}"
: "${DB_NAME:=wordpress}"
: "${DB_USER:=wp}"
: "${DB_PASS:=wp}"
: "${DB_HOST:=db}"

echo "Installing WordPress test suite..."

# Download WordPress core to the test suite's src/ directory
mkdir -p "$WP_CORE_DIR"
curl -sL "https://wordpress.org/${WP_VERSION}.tar.gz" | tar --strip-components=1 -xz -C "$WP_CORE_DIR"

# Download test suite files
mkdir -p "$WP_TESTS_DIR"
svn co --quiet https://develop.svn.wordpress.org/trunk/tests/phpunit/includes/ "$WP_TESTS_DIR/includes"
svn co --quiet https://develop.svn.wordpress.org/trunk/tests/phpunit/data/ "$WP_TESTS_DIR/data"

# Download wp-tests-config.php
wget -q -O "$WP_TESTS_DIR/wp-tests-config.php" https://develop.svn.wordpress.org/trunk/wp-tests-config-sample.php

# Update DB credentials in wp-tests-config.php
sed -i "s/youremptytestdbnamehere/$DB_NAME/" "$WP_TESTS_DIR/wp-tests-config.php"
sed -i "s/yourusernamehere/$DB_USER/" "$WP_TESTS_DIR/wp-tests-config.php"
sed -i "s/yourpasswordhere/$DB_PASS/" "$WP_TESTS_DIR/wp-tests-config.php"
sed -i "s|localhost|$DB_HOST|" "$WP_TESTS_DIR/wp-tests-config.php"

echo "✅ WordPress test suite installed in $WP_TESTS_DIR"