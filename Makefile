up:
	docker compose up -d --build

down:
	docker compose down -v

logs:
	docker compose logs -f

db-create:
	docker compose exec php php bin/console doctrine:database:create --if-not-exists

db-drop:
	docker compose exec php php bin/console doctrine:database:drop --if-exists --force

migrate-diff:
	docker compose exec php php bin/console doctrine:migrations:diff

migrate:
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	docker compose exec php php bin/console doctrine:fixtures:load --no-interaction

prepare-test-db:
	docker compose exec php php bin/console doctrine:database:drop --if-exists --force --env=test
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

xdebug-on:
	docker compose exec php sh -c 'echo "xdebug.mode=debug" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && kill -USR2 1'
	@echo "✅ Xdebug enabled (mode=debug)"

xdebug-off:
	docker compose exec php sh -c 'echo "xdebug.mode=off" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && kill -USR2 1'
	@echo "🧹 Xdebug disabled"

fix:
	docker compose exec php vendor/bin/ecs check src --fix
	docker compose exec php vendor/bin/rector process

baseline:
	docker compose exec php vendor/bin/phpstan -b

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

keycloak-refresh:
	curl -s http://localhost:8081/realms/symfony/protocol/openid-connect/certs \
	| jq -r '.keys[0].x5c[0]' \
	| awk '{print "-----BEGIN CERTIFICATE-----\n" $$0 "\n-----END CERTIFICATE-----"}' \
	> config/jwt/keycloak_public.pem
	@echo "✅ Updated public key from Keycloak JWKS"
