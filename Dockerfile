FROM wordpress:6.5-php8.2-apache

RUN curl -o wordpress.tar.gz https://wordpress.org/latest.tar.gz \
  && tar -xzf wordpress.tar.gz -C /var/www/html --strip-components=1 \
  && rm wordpress.tar.gz

# Install WP-CLI and MySQL client
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
  && chmod +x wp-cli.phar \
  && mv wp-cli.phar /usr/local/bin/wp \
  && apt-get update \
  && apt-get install -y mariadb-client wget subversion

# Install Node.js 18 and npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
  && apt-get update \
  && apt-get install -y nodejs

# Optional: Install build-essential for some npm packages
RUN apt-get install -y build-essential

# Install PHPUnit
RUN curl -L https://phar.phpunit.de/phpunit-9.phar -o /usr/local/bin/phpunit \
  && chmod +x /usr/local/bin/phpunit \
  && phpunit --version

# Confirm tools
RUN php -v && node -v && npm -v && wp --info

# ✅ Default command to start Apache in foreground (so container stays running)
CMD ["apache2-foreground"]