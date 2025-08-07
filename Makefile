# Makefile for dmg-read-more-link development

# Plugin path inside the container
PLUGIN_PATH=/var/www/html/wp-content/plugins/dmg-read-more-link

.PHONY: up down build seed login php-test js-test build-js clean restart

# Start all services in the background
up:
	docker compose up -d

# Stop and remove all containers
down:
	docker compose down -v

# Build the Docker images (WordPress with Node + WP-CLI)
build:
	docker compose build --no-cache

# Rebuild JS block (inside the container)
build-js:
	docker compose exec wordpress npm run build --prefix $(PLUGIN_PATH)

# Run PHP unit tests (inside the container)
php-test:
	docker compose exec wordpress phpunit --configuration=/var/www/html/wp-content/plugins/dmg-read-more-link/phpunit.xml.dist

# Run JavaScript tests using Jest (inside the container)
js-test:
	docker compose exec wordpress npm test --prefix $(PLUGIN_PATH)

# Re-run seed script manually (in case data reset)
seed:
	docker compose exec wordpress bash /docker-entrypoint-initwp.d/wp-seed.sh

# Full reset and rebuild
restart: down build up
	@echo "✅ Restart complete."

# Remove generated assets and reset DB (optional cleanup)
clean: down
	rm -f stress-test-results.csv cli-performance.png
	docker volume rm read-more-link-dev_db_data || true

install-tests:
	docker compose exec wordpress bash /var/www/html/wp-content/plugins/dmg-read-more-link/install-wp-tests.sh wordpress wp wp db

reset:
	docker compose down -v
	docker compose build --no-cache
	docker compose up
