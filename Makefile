#!make
include .env
include .env.local
export $(shell sed 's/=.*//' .env)
export $(shell sed 's/=.*//' .env.local)

# Provides a bash in PHP container (user www-data)
bash-php: up
	docker-compose exec -u www-data php bash

# Provides a bash in PHP container (user root)
bash-php-root: up
	docker-compose exec php bash

run-tests: up
	docker-compose exec -u www-data php bin/phpunit --coverage-html=public/tests

# Build app
install-app: build composer-install migrate

	# Configure Yarn storage
	docker-compose exec -u www-data php yarn config set global-folder ${YARN_GLOBAL_FOLDER}
	docker-compose exec -u www-data php yarn config set cache-folder ${YARN_CACHE_FOLDER}

	# Install Yarn dependencies
	docker-compose exec -u www-data php yarn install

	# Compile application assets
	docker-compose exec -u www-data php yarn encore production

composer-install: up
	# Install PHP dependencies
	docker-compose exec -u www-data php composer install

dsu-dump: up
	docker-compose exec -u www-data php php bin/console doctrine:schema:update --dump-sql

dsu-force: up
	docker-compose exec -u www-data php php bin/console doctrine:schema:update --force

# Migrate database with differences
migrate: up
	docker-compose exec -u www-data php php bin/console doctrine:migrations:migrate

make-migration: up
	docker-compose exec -u www-data php php bin/console make:migration


cache-clear: up
	docker-compose exec -u www-data php php bin/console cac:c

fixtures-load: up
	docker-compose exec -u www-data php php bin/console doctrine:fixtures:load

serve: up
	docker-compose exec -u www-data php symfony server:ca:install
	docker-compose exec -u www-data php symfony local:server:start --allow-http

# Up containers
up:
	docker-compose up -d
	docker-compose exec php usermod -u ${HOST_UID} www-data
#	docker-compose exec apache usermod -u ${HOST_UID} www-data

# Up containers, with build forced
build:
	docker-compose up -d --build
	docker-compose exec php usermod -u ${HOST_UID} www-data
# Down containers
down:
	docker-compose down
