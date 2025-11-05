up:
	docker compose up -d --build

down:
	docker compose down -v

logs:
	docker compose logs -f

db-create:
	docker compose exec php php bin/console doctrine:database:create --if-not-exists

migrate:
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	docker compose exec php php bin/console doctrine:fixtures:load --no-interaction

prepare-test-db:
	docker compose exec php php bin/console doctrine:database:drop --force --env=test
	docker compose exec php php bin/console doctrine:database:create --if-not-exists --env=test
	docker compose exec php php bin/console doctrine:schema:create --env=test --no-interaction
	#docker compose exec php php bin/console doctrine:migrations:migrate --env=test --no-interaction
	docker compose exec php php bin/console doctrine:fixtures:load --env=test --no-interaction

test: prepare-test-db
	docker compose exec php php bin/phpunit --colors=always

coverage:
	docker compose exec php php bin/phpunit --coverage-html var/coverage

lint:
	docker compose exec php vendor/bin/phpstan analyse
	docker compose exec php vendor/bin/ecs check src
	docker compose exec php vendor/bin/rector process --dry-run

fix:
	docker compose exec php vendor/bin/ecs check src --fix
	docker compose exec php vendor/bin/rector process

analyse:
	docker compose exec php vendor/bin/phpstan analyse --memory-limit=1G

prepare:
	make down
	docker compose build --pull
	make up
	make db-create migrate fixtures
	make lint test

# Convenience alias for tailing PHP container logs
php-log:
	docker compose logs -f php

# Clean up cache & vendor clutter
clean:
	docker compose exec php rm -rf var/cache/*
	docker compose exec php rm -rf vendor/composer/installed.json
